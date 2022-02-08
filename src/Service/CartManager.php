<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use InvalidArgumentException;

class CartManager
{
    public function getCartItemTotal(?CartItem $cartItem): float
    {
        if ($cartItem === null) {
            throw new InvalidArgumentException("CartItem doesn't exist");
        }
        /** @var Product $product */
        $product = $cartItem->getProduct();
        return $cartItem->getQuantity() * $product->getUnitPrice();
    }
}
