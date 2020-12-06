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

namespace Headio\Phalcon\ServiceLayer\Helper;

use Phalcon\Helper\Str;
use function str_replace;
use function substr;
use function ucwords;

class Inflector
{
    /**
     * Return a camelized syntax string.
     */
    public static function camelize(string $str) : string
    {
        return str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $str)));
    }

    /**
     * Return a variablized syntax string; similiar to a camelized string,
     * but returns the first letter in lowercase.
     */
    public static function variablize(string $str) : string
    {
        $str = ucwords(static::camelize($str));

        return Str::lower($str[0]) . substr($str, 1);
    }
}
