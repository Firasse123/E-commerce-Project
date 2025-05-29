<?php
namespace App\Controller;

use App\Entity\Order;
use App\Form\CheckoutType;
use App\Service\CartService;
use App\Service\OrderService;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    #[Route('/checkout', name: 'order_checkout')]
    public function checkout(
        Request $request,
        CartService $cartService,
        OrderService $orderService
    ): Response {
        $user = $this->getUser();
        $cart = $cartService->getCartForUser($user);
        
        if ($cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('cart_index');
        }

        // Valider le panier avant de procéder à la commande
        $errors = $cartService->validateCart($cart);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('cart_index');
        }

        $order = new Order();
        $form = $this->createForm(CheckoutType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Préparer les données de livraison depuis le formulaire
                $deliveryData = [
                    'firstName' => $order->getDeliveryFirstName(),
                    'lastName' => $order->getDeliveryLastName(),
                    'address' => $order->getDeliveryAddress(),
                    'zipCode' => $order->getDeliveryZipCode(),
                    'city' => $order->getDeliveryCity(),
                    'phone' => $order->getDeliveryPhone(),
                ];

                // Créer la commande via le service
                $order = $orderService->createOrderFromCart($cart, $deliveryData);
                
                $this->addFlash('success', 'Votre commande a été validée avec succès!');
                return $this->redirectToRoute('order_success', ['id' => $order->getId()]);
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la commande: ' . $e->getMessage());
                return $this->redirectToRoute('cart_index');
            }
        }

        return $this->render('order/checkout.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
        ]);
    }

    #[Route('/success/{id}', name: 'order_success')]
    public function success(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/my-orders', name: 'order_my_orders')]
    public function myOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('order/my_orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/show/{id}', name: 'order_show')]
    public function show(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}