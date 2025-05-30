<?php
// src/EventListener/LoginListener.php

namespace App\EventListener;

use App\Entity\User;
use App\Service\CartService;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginListener
{
    private CartService $cartService;
    private RequestStack $requestStack;

    public function __construct(CartService $cartService, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Ensure user has a cart
        $cart = $this->cartService->getCartForUser($user);

        // Optional: store cart ID in session if needed
        $session = $this->requestStack->getSession();
        $session->set('cart_id', $cart->getId());
    }
}
