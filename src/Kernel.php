<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    private const CONFIG_EXTS        = '.{php,xml,yaml,yml}';
    private const RESOURCE_TYPE_GLOB = 'glob';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $bundlesResource = new FileResource($this->getProjectDir() . '/config/bundles.php');
        $container->addResource($bundlesResource);

        $container->setParameter('container.dumper.inline_class_loader', \PHP_VERSION_ID < 70400 || $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);

        $this->loadPackage('/*' . self::CONFIG_EXTS, $loader);
        $this->loadPackage("/{$this->environment}/*", $loader);

        $this->loadService(self::CONFIG_EXTS, $loader);
        $this->loadService('_' . $this->environment . self::CONFIG_EXTS, $loader);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $this->importRoute("/{$this->environment}/*", $routes);
        $this->importRoute('/*' . self::CONFIG_EXTS, $routes);
        $this->importRoute(self::CONFIG_EXTS, $routes);
    }

    private function loadPackage(string $packageDir, LoaderInterface $loader): void
    {
        $packagesDir = $this->getProjectDir() . '/config/{packages}';
        $loader->load($packagesDir . $packageDir, self::RESOURCE_TYPE_GLOB);
    }

    private function loadService(string $serviceDir, LoaderInterface $loader): void
    {
        $servicesDir = $this->getProjectDir() . '/config/{services}';
        $loader->load($servicesDir . $serviceDir, self::RESOURCE_TYPE_GLOB);
    }

    private function importRoute(string $routeDir, RouteCollectionBuilder $routes): void
    {
        $routesDir = $this->getProjectDir() . '/config/{routes}';
        $routes->import($routesDir . $routeDir, '/', self::RESOURCE_TYPE_GLOB);
    }
}
