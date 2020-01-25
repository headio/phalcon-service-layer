<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
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
                        'host' => getenv('MEMCACHED_HOST'),
                        'port' => getenv('MEMCACHED_PORT'),
                        'weight' => getenv('MEMCACHED_WEIGHT')
                    ],
                ],
                'client' => [
                    \Memcached::OPT_PREFIX_KEY => getenv('MEMCACHED_PREFIX_KEY'),
                ],
            ],
        ],
    ],
    'database' => [
        'adapter' => getenv('DB_ADAPTER'),
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWD'),
        'dbname' => getenv('DB_NAME'),
        'charset' => getenv('DB_CHARSET'),
        'options' => [
            \PDO::ATTR_STRINGIFY_FETCHES  => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . getenv('DB_CHARSET')
        ],
    ],
    'debug' => true,
    'locale' => 'en_GB',
    'logPath' => 'tests' .
        DIRECTORY_SEPARATOR . '_data' .
        DIRECTORY_SEPARATOR . '_output' .
        DIRECTORY_SEPARATOR . 'Var' .
        DIRECTORY_SEPARATOR . 'Log' .
        DIRECTORY_SEPARATOR . 'Web' .
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
