#!/usr/bin/env bash
#
# This file is part of the Phalcon Framework.
#
# (c) Phalcon Team <team@phalcon.io>
#
# For the full copyright and license information, please view the
# LICENSE.txt file that was distributed with this source code.

# -e  Exit immediately if a command exits with a non-zero status.
# -u  Treat unset variables as an error when substituting.
set -eu

PHP_INI="$(phpenv prefix)/etc/php.ini"
PHP_CONF_D="$(phpenv prefix)/etc/conf.d"

(>&1 echo 'Install msgpack extension ...')
printf "\\n" | pecl install --force msgpack 1> /dev/null

(>&1 echo 'Install igbinary extension ...')
printf "\\n" | pecl install --force igbinary 1> /dev/null

(>&1 echo 'Install memcached extension ...')
printf "\\n" | pecl install --force memcached 1> /dev/null
