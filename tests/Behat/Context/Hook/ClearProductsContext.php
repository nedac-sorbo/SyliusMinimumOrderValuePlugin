<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;

final class ClearProductsContext implements Context
{
    private ProductRepositoryInterface $productRepository;
    private OrderItemRepositoryInterface $orderItemRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->productRepository = $productRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @BeforeScenario
     */
    public function clearProducts(BeforeScenarioScope $scope): void
    {
        $orderItems = $this->orderItemRepository->findAll();
        foreach ($orderItems as $orderItem) {
            $this->orderItemRepository->remove($orderItem);
        }

        $products = $this->productRepository->findAll();
        foreach ($products as $product) {
            $this->productRepository->remove($product);
        }
    }
}
