<?php
// src/Service/CartService.php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItems;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
    ) {}

    /**
     * Récupère ou crée un panier pour l'utilisateur
     */
    public function getCartForUser(User $user): Cart
    {
        return $this->cartRepository->findOrCreateForUser($user);
    }

    /**
     * Ajoute un produit au panier
     */
    public function addToCart(User $user, Product $product, int $quantity = 1): bool
    {
        if (!$product->isInStock() || $product->getStock() < $quantity) {
            return false;
        }

        $cart = $this->getCartForUser($user);
        
        // Vérifier si le produit est déjà dans le panier
        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct() === $product) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $newQuantity = $existingItem->getQuantity() + $quantity;
            if ($newQuantity > $product->getStock()) {
                return false;
            }
            $existingItem->setQuantity($newQuantity);
        } else {
            $cartItem = new CartItems();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            
            $cart->addCartItem($cartItem);
            $this->entityManager->persist($cartItem);
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
        
        return true;
    }

    /**
     * Met à jour la quantité d'un produit dans le panier
     */
    public function updateQuantity(User $user, Product $product, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($user, $product);
        }

        if ($quantity > $product->getStock()) {
            return false;
        }

        $cart = $this->getCartForUser($user);
        
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct() === $product) {
                $item->setQuantity($quantity);
                $cart->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->flush();
                return true;
            }
        }
        
        return false;
    }

    /**
     * Supprime un produit du panier
     */
    public function removeFromCart(User $user, Product $product): bool
    {
        $cart = $this->getCartForUser($user);
        
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct() === $product) {
                $cart->removeCartItem($item);
                $this->entityManager->remove($item);
                $cart->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->flush();
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vide le panier
     */
    public function clearCart(User $user): void
    {
        $cart = $this->getCartForUser($user);
        
        foreach ($cart->getCartItems() as $item) {
            $this->entityManager->remove($item);
        }
        
        $cart->clear();
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    /**
     * Vérifie la disponibilité des produits dans le panier
     */
    public function validateCart(Cart $cart): array
    {
        $errors = [];
        
        foreach ($cart->getCartItems() as $item) {
            $product = $item->getProduct();
            
            if (!$product->isActive()) {
                $errors[] = "Le produit '{$product->getName()}' n'est plus disponible.";
            } elseif ($item->getQuantity() > $product->getStock()) {
                $errors[] = "Stock insuffisant pour '{$product->getName()}'. Stock disponible: {$product->getStock()}";
            }
        }
        
        return $errors;
    }

    /**
     * Calcule le nombre total d'articles dans le panier
     */
    public function getCartItemCount(User $user): int
    {
        $cart = $this->getCartForUser($user);
        return $cart->getTotalItems();
    }
}