<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartService $cartService,
        private EmailService $emailService
    ) {}

    /**
     * Crée une commande à partir du panier
     */
    public function createOrderFromCart(Cart $cart, array $deliveryData): Order
    {
        // Valider le panier
        $errors = $this->cartService->validateCart($cart);
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        $order = new Order();
        $order->setUser($cart->getUser());
        $order->setTotalPrice((string) $cart->getTotalPrice());

        // Informations de livraison
        $order->setDeliveryFirstName($deliveryData['firstName']);
        $order->setDeliveryLastName($deliveryData['lastName']);
        $order->setDeliveryAddress($deliveryData['address']);
        $order->setDeliveryZipCode($deliveryData['zipCode']);
        $order->setDeliveryCity($deliveryData['city']);
        $order->setDeliveryPhone($deliveryData['phone'] ?? null);

        // Détails de la commande
        $orderDetails = [];
        foreach ($cart->getCartItems() as $item) {
            $product = $item->getProduct();
            
            $orderDetails[] = [
                'productId' => $product->getId(),
                'productName' => $product->getName(),
                'productPrice' => $product->getPrice(),
                'quantity' => $item->getQuantity(),
                'totalPrice' => $item->getTotalPrice()
            ];

            // Décrémenter le stock
            $product->setStock($product->getStock() - $item->getQuantity());
        }
        
        $order->setOrderDetails($orderDetails);
        
        $this->entityManager->persist($order);
        
        // Vider le panier
        $this->cartService->clearCart($cart->getUser());
        
        $this->entityManager->flush();

        // Envoyer l'email de confirmation
        $this->emailService->sendOrderConfirmation($order);
        
        return $order;
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateOrderStatus(Order $order, string $status): void
    {
        $order->setStatus($status);
        $order->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->flush();

        // Envoyer un email de mise à jour si nécessaire
        if (in_array($status, ['shipped', 'delivered'])) {
            $this->emailService->sendOrderStatusUpdate($order);
        }}


public function checkout(array $cart, Security $security, EntityManagerInterface $em)
{
    $user = $security->getUser(); // Get logged-in user

    // 1. Create a new Order
    $order = new Order();
    $order->setUser($user);
    $order->setCreatedAt(new \DateTimeImmutable());

    // 2. Loop through cart items
    foreach ($cart as $cartItem) {
        $product = $cartItem['product']; // instance of Product
        $quantity = $cartItem['quantity'];

        // Create OrderItem
        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setOrder($order); // Link to order

        // Add to Order
        $order->getOrderItems()->add($orderItem);

        // (Optional) Update product stock and totalSold
        $product->setStock($product->getStock() - $quantity);
        $product->incrementTotalSold($quantity);
    }

    // 3. Save to DB
    $em->persist($order); // orderItems are cascaded
    $em->flush();
}


    }
    