<?php

namespace App\Tests\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\CartManager;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartManagerTest extends KernelTestCase
{
    public function testGetCartItemTotalQuantityZero(): void
    {
        self::bootKernel();

        /** @var CartManager $cartManager */
        $cartManager = static::getContainer()->get(CartManager::class);
        $item = $this->createCartItem(0, 1);
        $this->assertEquals(0, $cartManager->getCartItemTotal($item));
    }

    public function testGetCartItemTotalProductPriceZero(): void
    {
        self::bootKernel();

        /** @var CartManager $cartManager */
        $cartManager = static::getContainer()->get(CartManager::class);
        $item = $this->createCartItem(1, 0);
        $this->assertEquals(0, $cartManager->getCartItemTotal($item));
    }

    public function testGetCartItemTotal(): void
    {
        self::bootKernel();

        /** @var CartManager $cartManager */
        $cartManager = static::getContainer()->get(CartManager::class);
        $item = $this->createCartItem(3, 2);
        $this->assertEquals(6, $cartManager->getCartItemTotal($item));
    }

    public function testGetNullCartItemTotal(): void
    {
        self::bootKernel();

        /** @var CartManager $cartManager */
        $cartManager = static::getContainer()->get(CartManager::class);
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals(6, $cartManager->getCartItemTotal(null));
    }

    private function createCartItem(int $quantity, float $unitPrice): CartItem
    {
        $product = new Product();
        $product->setUnitPrice($unitPrice);

        $item = new CartItem();
        $item->setQuantity($quantity);
        $item->setProduct($product);

        return $item;
    }
}
