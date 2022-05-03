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
    'baseUri' => '/',
    'cache' => [
        'modelCache' => [
            'adapter' => $_SERVER['CACHE_ADAPTER'],
            'options' => [
                'defaultSerializer' => $_SERVER['CACHE_SERIALIZER'],
                'host' => $_SERVER['CACHE_HOST'],
                'port' => (int) $_SERVER['CACHE_PORT'],
                'auth' => $_SERVER['CACHE_AUTH'],
                'index' => $_SERVER['CACHE_INDEX'],
                'lifetime' => (int) $_SERVER['CACHE_LIFETIME'],
                'persistent' => (bool) $_SERVER['CACHE_PERSISTENT'],
                'prefix' => $_SERVER['CACHE_PREFIX'],
                'socket' => $_SERVER['CACHE_SOCKET'],
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
    'dispatcher' => [
        'defaultAction' => 'index',
        'defaultController' => 'Index',
        'defaultControllerNamespace' => 'Stub\\Module\\Admin\\Controller',
        'defaultModule' => 'Admin'
    ],
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
    'modules' => [
        'Admin' => [
            'className' => 'Stub\\Module\\Admin\\Module',
            'path' => TEST_STUB_DIR . 'Module/Admin/Module.php',
            'metadata' => [
                'controllersNamespace' => 'Stub\\Module\\Admin\\Controller'
            ]
        ],
    ],
    'paginator' => [
        'cursor' => [
            'queryIdentifiers' => [
                'before' => 'prev',
                'after' => 'next',
            ],
        ],
    ],
    'routes' => [
        'Admin' => [
            'Stub\Module\Admin\Controller\Tag' => '/tags',
        ],
    ],
    'services' => [
        'Stub\Provider\EventManager',
        'Stub\Provider\Logger',
        'Stub\Provider\Dispatcher',
        'Stub\Provider\Request',
        'Stub\Provider\Router',
        'Stub\Provider\Annotation',
        'Stub\Provider\CacheManager',
        'Stub\Provider\Db',
        'Stub\Provider\i18n',
        'Stub\Provider\ModelCache',
        'Stub\Provider\ModelMetaData',
        'Stub\Provider\ModelManager',
        'Stub\Provider\TransactionManager',
        'Stub\Provider\Url',
        'Stub\Provider\View',
    ],
    'timezone' => 'Europe/London',
    'view' => [
        'defaultPath' => TEST_OUTPUT_DIR . 'Module/Admin/View/',
        'compiledPath' => TEST_OUTPUT_DIR . 'Cache/Volt/',
        'compiledSeparator' => '_',
    ]
];
