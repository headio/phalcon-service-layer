#!/usr/bin/env bash
#
# This file is part of the Phalcon.
#
# (c) Phalcon Team <team@phalconphp.com>
#
# For the full copyright and license information, please view
# the LICENSE.txt file that was distributed with this source code.
set -eu

(>&1 echo 'Install psr extension ...')
printf "\\n" | pecl install --force psr 1> /dev/null

(>&1 echo 'Install memcached extension ...')
printf "\\n" | pecl install --force memcached 1> /dev/null
