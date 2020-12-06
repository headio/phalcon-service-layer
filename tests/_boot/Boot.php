<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);
/**
 * Codeception test bootstrap.
 */
use Dotenv\Dotenv;
use Headio\Phalcon\Bootstrap\Application\Factory as AppFactory;
use Headio\Phalcon\Bootstrap\Di\Factory as DiFactory;
use Phalcon\Config;

chdir(dirname(__DIR__, 2));
require_once 'vendor/autoload.php';

try {
    $env = Dotenv::createImmutable(dirname(__DIR__, 2));
    $env->load();
    $config = new Config(
        require 'tests/_boot/Config.php'
    );
    $di = (new DiFactory($config))->createDefaultMvc();
    $app = (new AppFactory($di))->createForMvc();
    return $app;
} catch (\Throwable $e) {
    echo $e->getMessage();
}
