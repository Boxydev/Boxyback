<?php

namespace Boxydev\Boxyback\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BackupConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('boxyback');
        $rootNode
            ->children()
                ->arrayNode('boxyback')
                    ->children()
                        ->arrayNode('apps')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('folder')
                                        ->isRequired()->cannotBeEmpty()
                                    ->end()
                                    ->arrayNode('mysql')
                                        ->children()
                                            ->scalarNode('host')
                                                ->defaultValue('localhost')
                                            ->end()
                                            ->scalarNode('database')
                                                ->isRequired()->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('user')
                                                ->isRequired()->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('password')
                                                ->isRequired()->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->requiresAtLeastOneElement()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('cloud')
                            ->children()
                                ->scalarNode('local')
                                    ->isRequired()->cannotBeEmpty()
                                ->end()
                                ->arrayNode('ftp')
                                    ->children()
                                        ->scalarNode('host')
                                            ->isRequired()->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('login')
                                            ->isRequired()->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('password')
                                            ->isRequired()->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('email')->end()
                                        ->scalarNode('vm')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->isRequired()
                        ->end()
                    ->end()
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}