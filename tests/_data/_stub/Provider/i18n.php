<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use function file_exists;
use function substr;

class i18n implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'i18n',
            function () use ($di) {
                $config = $di->get('config');
                $locale = $config->locale;

                if (!$config->useI18n) {
                    $locale = $config->locale;
                }

                $translations = $config->applicationPath . 'Translation'.
                    DIRECTORY_SEPARATOR . 'Message'.
                    DIRECTORY_SEPARATOR . $locale . '.php';
                // Normalize locale to ISO 639-1 (alpha-2 format), if
                // no local language dialect exists for this locale
                if (!file_exists($translations)) {
                    $locale = substr($locale, 0, 2);
                    $translations = $config->applicationPath . 'Translation'.
                    DIRECTORY_SEPARATOR . 'Message'.
                    DIRECTORY_SEPARATOR . $locale . '.php';
                }

                $interpolator = new InterpolatorFactory();
                $factory = new TranslateFactory($interpolator);

                return $factory->newInstance(
                    'array',
                    [
                        'defaultInterpolator' => 'indexedArray',
                        'content' => file_exists($translations) ? include $translations : [],
                    ]
                );
            }
        );
    }
}
