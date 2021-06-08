<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webmozart\Assert\Assert;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nedac_sylius_minimum_order_value_plugin');

        $rootNode = $treeBuilder->getRootNode();

        Assert::isInstanceOf($rootNode, ArrayNodeDefinition::class);

        /** @phpstan-ignore-next-line */
        $rootNode
            ->children()
                ->arrayNode('checkout_resolver')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('pattern')
                            ->defaultValue('/checkout/.+')
                            ->validate()
                            ->ifTrue(function ($pattern): bool {
                                return !is_string($pattern);
                            })
                                ->thenInvalid('Invalid pattern "%s"')
                            ->end()
                        ->end()
                        ->arrayNode('route_map')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('route')
                                        ->cannotBeEmpty()
                                        ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
