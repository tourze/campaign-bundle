<?php

namespace CampaignBundle\Tests;

use CampaignBundle\CampaignBundle;
use CampaignBundle\Tests\Entity\TestUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

abstract class BaseTestCase extends KernelTestCase
{
    protected static function createKernel(array $options = []): KernelInterface
    {
        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        // 获取 CampaignBundle 的依赖并加载
        $bundles = [
            CampaignBundle::class => ['all' => true],
        ];
        
        // 递归加载所有依赖的 Bundle
        $dependencies = CampaignBundle::getBundleDependencies();
        foreach ($dependencies as $bundleClass => $envs) {
            $bundles[$bundleClass] = $envs;
            
            // 如果该 Bundle 也实现了 BundleDependencyInterface，递归加载其依赖
            if (is_subclass_of($bundleClass, \Tourze\BundleDependency\BundleDependencyInterface::class)) {
                $subDependencies = $bundleClass::getBundleDependencies();
                foreach ($subDependencies as $subBundleClass => $subEnvs) {
                    if (!isset($bundles[$subBundleClass])) {
                        $bundles[$subBundleClass] = $subEnvs;
                    }
                }
            }
        }

        return new IntegrationTestKernel(
            $env,
            $debug,
            $bundles,
            [
                'CampaignBundle\Tests\Entity' => __DIR__ . '/Entity',
            ],
            function (ContainerBuilder $container) {
                if ($container->hasExtension('doctrine')) {
                    $container->prependExtensionConfig('doctrine', [
                        'orm' => [
                            'resolve_target_entities' => [
                                UserInterface::class => TestUser::class,
                            ],
                        ],
                    ]);
                }
            }
        );
    }
}