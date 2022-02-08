<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartRepository $cartRepository, CartManager $cartManager): Response
    {
        /** @var Cart $cart */
        $cart = $cartRepository->find(1);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'cartManager' => $cartManager
        ]);
    }
}
