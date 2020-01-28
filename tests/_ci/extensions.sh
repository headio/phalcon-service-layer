#!/usr/bin/env bash
#
# This file is part of the Phalcon.
#
# (c) Phalcon Team <team@phalconphp.com>
#
# For the full copyright and license information, please view
# the LICENSE.txt file that was distributed with this source code.

if [[ "$(php-config --vernum)" -ge 70300 ]] || [[ -f "$(php-config --extension-dir)/memcached.so" ]]
then
  git clone \
    --depth=1 \
    https://github.com/php-memcached-dev/php-memcached memcached

  cd memcached || exit 1

  $(phpenv which phpize)
  ./configure --with-php-config="$(phpenv which php-config)" --enable-memcached 1>/dev/null
  make --silent -j"$(getconf _NPROCESSORS_ONLN)" 1>/dev/null
  make --silent install 1>/dev/null
fi

echo "extension=memcached.so" >> "$(phpenv prefix)/etc/conf.d/memcached.ini"

