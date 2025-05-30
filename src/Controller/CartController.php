<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        $user = $this->getUser();
        $cart = $cartService->getCartForUser($user);
        
        // Valider le panier avant affichage
        $errors = $cartService->validateCart($cart);
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('warning', $error);
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'errors' => $errors,
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, CartService $cartService): Response
    {
        $user = $this->getUser();
        $quantity = $request->request->getInt('quantity', 1);
        
        $success = $cartService->addToCart($user, $product, $quantity);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => $success,
                'message' => $success ? 'Produit ajouté au panier' : 'Impossible d\'ajouter le produit (stock insuffisant)',
                'cartCount' => $cartService->getCartItemCount($user)
            ]);
        }

        if ($success) {
            $this->addFlash('success', 'Produit ajouté au panier avec succès!');
        } else {
            $this->addFlash('error', 'Impossible d\'ajouter le produit au panier (stock insuffisant)');
        }
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove')]
    public function remove(Product $product, CartService $cartService): Response
    {
        $user = $this->getUser();
        $success = $cartService->removeFromCart($user, $product);
        
        if ($success) {
            $this->addFlash('success', 'Produit retiré du panier');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression du produit');
        }
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(Product $product, Request $request, CartService $cartService): Response
    {
        $user = $this->getUser();
        $quantity = $request->request->getInt('quantity', 1);
        
        $success = $cartService->updateQuantity($user, $product, $quantity);

        if ($request->isXmlHttpRequest()) {
            $cart = $cartService->getCartForUser($user);
            return new JsonResponse([
                'success' => $success,
                'message' => $success ? 'Quantité mise à jour' : 'Stock insuffisant',
                'total' => $cart->getTotal(),
                'cartCount' => $cartService->getCartItemCount($user)
            ]);
        }

        if ($success) {
            $this->addFlash('success', 'Quantité mise à jour');
        } else {
            $this->addFlash('error', 'Stock insuffisant pour cette quantité');
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/clear', name: 'cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $user = $this->getUser();
        $cartService->clearCart($user);
        $this->addFlash('success', 'Panier vidé');
        
        return $this->redirectToRoute('cart_index');
    }
}