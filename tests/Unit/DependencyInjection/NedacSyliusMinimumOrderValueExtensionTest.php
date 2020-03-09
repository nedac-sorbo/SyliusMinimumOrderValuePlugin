<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Nedac\SyliusMinimumOrderValuePlugin\DependencyInjection\NedacSyliusMinimumOrderValueExtension;

final class NedacSyliusMinimumOrderValueExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @inheritDoc
     */
    protected function getContainerExtensions(): array
    {
        return [
            new NedacSyliusMinimumOrderValueExtension()
        ];
    }

    public function testCanLoadCheckoutResolverServicesByDefault(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.resolver.checkout');
        $this->assertContainerBuilderHasService('sylius.router.checkout_state');
        $this->assertContainerBuilderHasService('sylius.listener.checkout_redirect');
    }

    public function testDoesNotLoadCheckoutResolverServicesIfDisabled(): void
    {
        $this->load([
            'checkout_resolver' => [
                'enabled' => false,
            ],
        ]);

        $this->assertContainerBuilderNotHasService('sylius.resolver.checkout');
        $this->assertContainerBuilderNotHasService('sylius.router.checkout_state');
        $this->assertContainerBuilderNotHasService('sylius.listener.checkout_redirect');
    }
}
