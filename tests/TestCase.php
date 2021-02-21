<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <danyelkrizan@gmail.com>
 * Date: 30.01.21 8:40
 */

namespace Test;

use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\App;
use RuntimeException;

class TestCase extends \PHPUnit\Framework\TestCase {

    /** @var ContainerInterface|Container|null */
    protected $container;

    /** @var App|null */
    protected $app;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void {
        $this->bootApp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void {
        $this->shutdownApp();
    }

    /**
     * Bootstrap app.
     *
     * @return void
     */
    protected function bootApp(): void {
        $this->app = require __DIR__ . '/../public/boostrap.php';
        $this->container = $this->app->getContainer();
    }

    /**
     * Shutdown app.
     *
     * @return void
     */
    protected function shutdownApp(): void {
        $this->app = null;
        $this->container = null;
    }

    /**
     * Get container.
     *
     * @throws RuntimeException
     *
     * @return ContainerInterface|Container The container
     */
    protected function getContainer(): ContainerInterface {
        if ($this->container === null) {
            throw new RuntimeException('Container must be initialized');
        }

        return $this->container;
    }

    /**
     * Get app.
     *
     * @throws RuntimeException
     *
     * @return App The app
     */
    protected function getApp(): App {
        if ($this->app === null) {
            throw new RuntimeException('App must be initialized');
        }

        return $this->app;
    }
}