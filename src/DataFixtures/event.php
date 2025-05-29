<?php

namespace App\EventListener;
use App\Entity\User;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Gestionnaire d'événements pour les connexions utilisateur
 * Permet de gérer les actions à effectuer lors de la connexion
 */
class LoginListener
{
    private EntityManagerInterface $entityManager;
    private CartService $cartService;
    private RequestStack $requestStack;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartService $cartService,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * Gestionnaire pour l'événement de connexion réussie (Symfony 5.1+)
     * Utilise le nouvel événement LoginSuccessEvent
     */
    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        
        if (!$user instanceof User) {
            return;
        }

        $this->handleUserLogin($user, $event->getRequest());
    }

    /**
     * Gestionnaire pour l'événement de connexion interactive (legacy)
     * Maintenu pour compatibilité avec les versions antérieures
     */
    #[AsEventListener(event: SecurityEvents::INTERACTIVE_LOGIN)]
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        if (!$user instanceof User) {
            return;
        }

        $this->handleUserLogin($user, $event->getRequest());
    }

    /**
     * Traite les actions communes lors de la connexion d'un utilisateur
     */
    private function handleUserLogin(User $user, $request): void
    {
        try {
            // 1. Mise à jour de la dernière connexion
            $this->updateLastLogin($user);

            // 2. Gestion du panier
            $this->handleCartOnLogin($user, $request);

            // 3. Log de la connexion
            $this->logUserLogin($user, $request);

            // 4. Nettoyage des sessions expirées (optionnel)
            $this->cleanupExpiredSessions();

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du traitement de la connexion utilisateur', [
                'user_id' => $user->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Met à jour la date de dernière connexion de l'utilisateur
     * Note: Cette méthode est désactivée car l'entité User n'a pas d'attribut lastLoginAt
     */
    private function updateLastLogin(User $user): void
    {
        // Si vous voulez traquer les connexions, vous pouvez :
        // 1. Ajouter l'attribut lastLoginAt à votre entité User
        // 2. Ou créer une entité LoginHistory séparée
        // 3. Ou simplement logger l'information
        
        $this->logger->info('Connexion utilisateur enregistrée', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'login_time' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    
    private function handleCartOnLogin(User $user, $request): void
    {
        if (!$request) {
            return;
        }

        $session = $request->getSession();
        
        // Récupération du panier de session (panier anonyme)
        $sessionCart = $session->get('cart', []);
        
        if (!empty($sessionCart)) {
            // Fusion du panier de session avec le panier utilisateur
            $this->cartService->mergeSessionCartWithUserCart($user, $sessionCart);
            
            
            $session->remove('cart');
            
            $this->logger->info('Panier de session fusionné avec le panier utilisateur', [
                'user_id' => $user->getId(),
                'session_cart_items' => count($sessionCart)
            ]);
        }

        // Initialisation du panier utilisateur dans le service
        $this->cartService->initializeUserCart($user);
    }

    /**
     * Enregistre la connexion dans les logs
     */
    private function logUserLogin(User $user, $request): void
    {
        $logData = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'login_time' => new \DateTimeImmutable(),
        ];

        // Ajout d'informations sur la requête si disponible
        if ($request) {
            $logData['ip_address'] = $request->getClientIp();
            $logData['user_agent'] = $request->headers->get('User-Agent');
            $logData['referer'] = $request->headers->get('referer');
        }

        $this->logger->info('Connexion utilisateur réussie', $logData);

        // Log spécifique pour les administrateurs
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->logger->warning('Connexion administrateur', [
                'admin_user_id' => $user->getId(),
                'admin_email' => $user->getEmail(),
                'ip_address' => $request ? $request->getClientIp() : 'unknown'
            ]);
        }
    }

    /**
     * Nettoyage optionnel des sessions expirées
     * Peut être utilisé pour maintenir la base de données propre
     */
    private function cleanupExpiredSessions(): void
    {
        // Cette méthode peut être utilisée pour nettoyer les anciennes sessions
        // ou effectuer d'autres tâches de maintenance lors de la connexion
        
        // Exemple : supprimer les paniers abandonnés de plus de 30 jours
        try {
            $expiredDate = new \DateTimeImmutable('-30 days');
            
            $qb = $this->entityManager->createQueryBuilder();
            $qb->delete('App\Entity\Cart', 'c')
               ->where('c.updatedAt < :expiredDate')
               ->andWhere('c.user IS NULL') // Paniers anonymes seulement
               ->setParameter('expiredDate', $expiredDate);
            
            $deletedCount = $qb->getQuery()->execute();
            
            if ($deletedCount > 0) {
                $this->logger->info('Paniers expirés supprimés', [
                    'deleted_count' => $deletedCount
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du nettoyage des sessions expirées', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Méthode utilitaire pour déterminer si l'utilisateur est un nouvel utilisateur
     * Basé sur la date de création du compte (sans lastLoginAt)
     */
    private function isNewUser(User $user): bool
    {
        $created = $user->getCreatedAt();
        
        if (!$created) {
            return true;
        }
        
        // Considéré comme nouveau si le compte a été créé il y a moins de 5 minutes
        $now = new \DateTimeImmutable();
        $diff = $now->getTimestamp() - $created->getTimestamp();
        return $diff < 300; // 5 minutes
    }

    /**
     * Méthode pour gérer les nouveaux utilisateurs
     * Peut être appelée pour des actions spécifiques aux nouveaux utilisateurs
     */
    private function handleNewUser(User $user): void
    {
        // Actions spécifiques pour les nouveaux utilisateurs
        // Par exemple : envoi d'un email de bienvenue, attribution de points de fidélité, etc.
        
        $this->logger->info('Nouvel utilisateur connecté pour la première fois', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);
        
        // Exemple : marquer l'utilisateur comme ayant eu sa première connexion
        // $this->sendWelcomeEmail($user);
        // $this->grantWelcomeBonus($user);
    }
}