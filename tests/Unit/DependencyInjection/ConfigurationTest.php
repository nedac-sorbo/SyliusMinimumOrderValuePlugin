<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Unit\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Nedac\SyliusMinimumOrderValuePlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    public function testHasDefaultConfigurationForCheckoutResolverNode(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['checkout_resolver' => [
                'enabled' => true,
                'pattern' => '/checkout/.+',
                'route_map' => [],
            ]],
            'checkout_resolver'
        );
    }

    public function testCheckoutResolverPatternAcceptsOnlyStringValues(): void
    {
        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => 1,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => true,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => 1.24,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => [],
            ],
        ]]);
    }

    public function testCheckoutRouteMapIsConfigurable(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'checkout_resolver' => [
                        'route_map' => [
                            'addressed' => [
                                'route' => 'sylius_shop_checkout_select_shipping',
                            ],
                        ],
                    ]
                ],
            ],
            [
                'checkout_resolver' => [
                    'enabled' => true,
                    'pattern' => '/checkout/.+',
                    'route_map' => [
                        'addressed' => [
                            'route' => 'sylius_shop_checkout_select_shipping',
                        ],
                    ],
                ]
            ],
            'checkout_resolver'
        );
    }

    public function testCheckoutRouteMapRouteCannotBeEmpty(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'checkout_resolver' => [
                        'route_map' => [
                            'addressed' => [],
                        ],
                    ],
                ]
            ]
        );
    }
}
