<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use InvalidArgumentException;

class CartManager
{
    private ProductRepository $productRepository;
    private CartRepository $cartRepository;
    private CartItemRepository $cartItemRepository;

    public function __construct(ProductRepository $productRepository, CartRepository $cartRepository, CartItemRepository $cartItemRepository)
    {
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    public function getCartItemTotal(?CartItem $cartItem): float
    {
        if ($cartItem === null) {
            throw new InvalidArgumentException("CartItem doesn't exist");
        }
        /** @var Product $product */
        $product = $cartItem->getProduct();
        return $cartItem->getQuantity() * $product->getUnitPrice();
    }

    public function addProductToCart(int $productId, int $cartId): CartItem
    {
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            throw new InvalidArgumentException("Product $productId doesn't exist");
        }
        $cart = $this->cartRepository->find($cartId);
        if ($cart === null) {
            throw new InvalidArgumentException("Cart $cartId doesn't exist");
        }

        $cartItem = $this->cartItemRepository->findOneBy(['product' => $product, 'cart' => $cart]);
        if ($cartItem === null) {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setCart($cart);
            $cartItem->setQuantity(1);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        }

        return $cartItem;
    }
}
