<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */

return [
    'annotations' => [
        'adapter' => 'stream',
        'options' => [
            'annotationsDir' => TEST_OUTPUT_DIR . 'Cache/Annotation/',
        ],
    ],
    'applicationPath' => 'src' . DIRECTORY_SEPARATOR,
    'cache' => [
        'apply' => false,
        'modelCache' => [
            'adapter' => 'libmemcached',
            'options' => [
                'defaultSerializer' => $_SERVER['MEMCACHED_SERIALIZER'],
                'lifetime' => 3600 * 24 * 30,
                'prefix' => $_SERVER['MEMCACHED_PREFIX_KEY'],
                'servers' => [
                    [
                        'host' => $_SERVER['MEMCACHED_HOST'],
                        'port' => (int) $_SERVER['MEMCACHED_PORT'],
                        'weight' => (int) $_SERVER['MEMCACHED_WEIGHT']
                    ],
                ],
                'client' => [
                    \Memcached::OPT_CONNECT_TIMEOUT => 10,
                ],
            ],
        ],
    ],
    'database' => [
        'adapter' => $_SERVER['DB_ADAPTER'],
        'host' => $_SERVER['DB_HOST'],
        'port' => (int) $_SERVER['DB_PORT'],
        'username' => $_SERVER['DB_USER'],
        'password' => $_SERVER['DB_PASSWD'],
        'dbname' => $_SERVER['DB_NAME'],
        'charset' => $_SERVER['DB_CHARSET'],
        'options' => [
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $_SERVER['DB_CHARSET']
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
        'adapter' => 'Stream',
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
        'Stub\Service\TransactionManager'
    ],
    'timezone' => 'Europe/London',
];
