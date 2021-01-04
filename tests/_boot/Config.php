<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */

return [
    'annotations' => [
        'adapter' => 'Files',
        'options' => [
            'annotationsDir' => TEST_OUTPUT_DIR . 'Cache/Annotation/',
        ],
    ],
    'applicationPath' => 'src' . DIRECTORY_SEPARATOR,
    'cache' => [
        'modelCache' => [
            'apply' => false,
            'adapter' => 'Libmemcached',
            'lifetime' => 3600 * 24 * 30,
            'options' => [
                'statsKey' => '_PHCM',
                'servers' => [
                    [
                        'host' => $_ENV['MEMCACHED_HOST'],
                        'port' => (int) $_ENV['MEMCACHED_PORT'],
                        'weight' => (int) $_ENV['MEMCACHED_WEIGHT']
                    ],
                ],
                'client' => [
                    \Memcached::OPT_PREFIX_KEY => $_ENV['MEMCACHED_PREFIX_KEY'],
                ],
            ],
        ],
    ],
    'database' => [
        'adapter' => $_ENV['DB_ADAPTER'],
        'host' => $_ENV['DB_HOST'],
        'port' => (int) $_ENV['DB_PORT'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWD'],
        'dbname' => $_ENV['DB_NAME'],
        'charset' => $_ENV['DB_CHARSET'],
        'options' => [
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $_ENV['DB_CHARSET']
        ],
    ],
    'debug' => true,
    'locale' => 'en_GB',
    'logPath' => 'tests' .
        DIRECTORY_SEPARATOR . '_data' .
        DIRECTORY_SEPARATOR . '_output' .
        DIRECTORY_SEPARATOR . 'Var' .
        DIRECTORY_SEPARATOR . 'Log' .
        DIRECTORY_SEPARATOR,
    'metadata' => [
        'adapter' => 'Files',
        'options' => [
            'metaDataDir' => TEST_OUTPUT_DIR . 'Cache/Metadata/',
        ],
    ],
    'services' => [
        'Stub\Service\EventManager',
        'Stub\Service\Logger',
        'Stub\Service\Annotation',
        'Stub\Service\CacheManager',
        'Stub\Service\Db',
        'Stub\Service\ModelCache',
        'Stub\Service\ModelMetaData',
        'Stub\Service\ModelManager',
    ],
    'timezone' => 'Europe/London',
];
