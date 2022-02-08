<?php

namespace App\Tests\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
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

    public function testAddNewProductToCart()
    {
        $product = new Product();
        $product->setUnitPrice(1);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->any())
            ->method('find')
            ->willReturn($product);

        $cart = new Cart();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->any())
            ->method('find')
            ->willReturn($cart);

        $cartItemRepository = $this->createMock(CartItemRepository::class);
        $cartItemRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $cartManager = new CartManager($productRepository, $cartRepository, $cartItemRepository);

        /** @var CartItem $cartItem */
        $cartItem = $cartManager->addProductToCart(3, 1);

        $this->assertEquals(1, $cartItem->getQuantity());
    }

    public function testAddSameProductToCart()
    {
        $product = new Product();
        $product->setUnitPrice(1);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->any())
            ->method('find')
            ->willReturn($product);

        $cart = new Cart();
        $item = new CartItem();
        $item->setProduct($product);
        $item->setCart($cart);
        $item->setQuantity(3);
        $cart->addItem($item);

        $cartItemRepository = $this->createMock(CartItemRepository::class);
        $cartItemRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($item);

        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->any())
            ->method('find')
            ->willReturn($cart);

        $cartManager = new CartManager($productRepository, $cartRepository, $cartItemRepository);

        /** @var CartItem $cartItem */
        $cartItem = $cartManager->addProductToCart(3, 1);

        $this->assertEquals(4, $cartItem->getQuantity());
    }

    public function testAddNullProductToCart()
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->any())
            ->method('find')
            ->willReturn(null);

        $cart = new Cart();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->any())
            ->method('find')
            ->willReturn($cart);

        $cartItemRepository = $this->createMock(CartItemRepository::class);
        $cartItemRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $cartManager = new CartManager($productRepository, $cartRepository, $cartItemRepository);

        $this->expectException(InvalidArgumentException::class);
        $cartManager->addProductToCart(42, 1);
    }

    public function testAddProductToNullCart()
    {
        $product = new Product();
        $product->setUnitPrice(1);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->any())
            ->method('find')
            ->willReturn($product);

        $cart = new Cart();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->expects($this->any())
            ->method('find')
            ->willReturn(null);

        $cartItemRepository = $this->createMock(CartItemRepository::class);
        $cartItemRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $cartManager = new CartManager($productRepository, $cartRepository, $cartItemRepository);

        $this->expectException(InvalidArgumentException::class);
        $cartManager->addProductToCart(42, 1);
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
