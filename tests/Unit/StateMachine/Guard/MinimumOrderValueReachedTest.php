<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Unit\StateMachine\Guard;

use Mockery;
use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Nedac\SyliusMinimumOrderValuePlugin\StateMachine\Guard\MinimumOrderValueReached;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class MinimumOrderValueReachedTest extends TestCase
{
    private function getCalculatorMock(?int $subtotal = null): OrderItemsSubtotalCalculatorInterface
    {
        /** @var Mockery\MockInterface $calculator */
        $calculator = Mockery::mock(OrderItemsSubtotalCalculatorInterface::class);

        if (null !== $subtotal) {
            /** @var Mockery\Expectation $expectation */
            $expectation = $calculator->shouldReceive('getSubtotal');
            $expectation
                ->once()
                ->andReturn($subtotal)
            ;
        }

        /** @var OrderItemsSubtotalCalculatorInterface $calculator */
        return $calculator;
    }

    private function getOrderMock(?int $minimumOrderValue): OrderInterface
    {
        $order = Mockery::mock(OrderInterface::class);

        /** @var Mockery\Expectation $expectation */
        $expectation = $order->shouldReceive('getChannel');
        $expectation
            ->once()
            ->andReturn($this->getChannelMock($minimumOrderValue))
        ;

        /** @var OrderInterface $order */
        return $order;
    }

    private function getChannelMock(?int $minimumOrderValue): ChannelInterface
    {
        $channel = Mockery::mock(ChannelInterface::class);

        /** @var Mockery\Expectation $expectation */
        $expectation = $channel->shouldReceive('getMinimumOrderValue');
        $expectation
            ->once()
            ->andReturn($minimumOrderValue)
        ;

        /** @var ChannelInterface $channel */
        return $channel;
    }

    public function testCanInstantiate(): void
    {
        $guard = new MinimumOrderValueReached($this->getCalculatorMock());
        self::assertNotNull($guard);
    }

    private function doTestGuard(bool $equals, ?int $minimumOrderValue = null, ?int $subtotal = null): void
    {
        $guard = new MinimumOrderValueReached($this->getCalculatorMock($subtotal));
        $result = $guard->isMinimumOrderValueReached($this->getOrderMock($minimumOrderValue));

        self::assertEquals($equals, $result);
    }

    /**
     * @depends testCanInstantiate
     */
    public function testCanDetermineThatMinimumOrderValueIsReached(): void
    {
        $this->doTestGuard(true, 10000, 100000);
    }

    /**
     * @depends testCanInstantiate
     */
    public function testCanHandleNoConfiguredMinimumValue(): void
    {
        $this->doTestGuard(true);
    }

    /**
     * @depends testCanInstantiate
     */
    public function testCanDetermineThatMinimumOrderValueIsNotReached(): void
    {
        $this->doTestGuard(false, 100000, 10000);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
