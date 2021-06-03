<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuote;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;
use Tavy315\SyliusQuotesPlugin\Form\Type\CustomerQuoteType;
use Tavy315\SyliusQuotesPlugin\Repository\CustomerQuoteRepository;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tavy315_sylius_quotes');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end();
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer_quote')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CustomerQuoteType::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomerQuoteInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('model')->defaultValue(CustomerQuote::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CustomerQuoteRepository::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
