<?php
// src/Service/EmailService.php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    /**
     * Envoie un email de confirmation de commande
     */
    public function sendOrderConfirmation(Order $order): void
    {
        $email = (new Email())
            ->from('noreply@men-clothing-store.com')
            ->to($order->getUser()->getEmail())
            ->subject('Confirmation de votre commande #' . $order->getReference())
            ->html($this->twig->render('emails/order_confirmation.html.twig', [
                'order' => $order
            ]));

        $this->mailer->send($email);
    }

    /**
     * Envoie un email de mise à jour du statut de commande
     */
    public function sendOrderStatusUpdate(Order $order): void
    {
        $subject = match($order->getStatus()) {
            'shipped' => 'Votre commande a été expédiée',
            'delivered' => 'Votre commande a été livrée',
            default => 'Mise à jour de votre commande'
        };

        $email = (new Email())
            ->from('noreply@men-clothing-store.com')
            ->to($order->getUser()->getEmail())
            ->subject($subject . ' #' . $order->getReference())
            ->html($this->twig->render('emails/order_status_update.html.twig', [
                'order' => $order
            ]));

        $this->mailer->send($email);
    }

    /**
     * Envoie un email de bienvenue
     */
    public function sendWelcomeEmail(User $user): void
    {
        $email = (new Email())
            ->from('noreply@men-clothing-store.com')
            ->to($user->getEmail())
            ->subject('Bienvenue sur Men Clothing Store')
            ->html($this->twig->render('emails/welcome.html.twig', [
                'user' => $user
            ]));

        $this->mailer->send($email);
    }
}