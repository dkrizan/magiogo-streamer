<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <danyelkrizan@gmail.com>
 * Date: 30.01.21 8:38
 */

namespace Test;

use Noodlehaus\Config;

class IntegrationTestCase extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        /** @var Config $config */
//        $config = $this->container->get('config');
//        $config->merge(new Config(__DIR__ . '/../../config.yaml'));
    }
}