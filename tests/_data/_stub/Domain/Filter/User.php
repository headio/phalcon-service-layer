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

namespace Stub\Domain\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Filter;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;

class User extends Filter
{
    private ?string $email = null;

    private ?int $primaryKey = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $val): FilterInterface
    {
        $this->email = $val;

        return $this->eq('email', $val);
    }

    public function getPrimaryKey(): ?int
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey(int $val): FilterInterface
    {
        $this->primaryKey = $val;

        return $this->eq('id', $val);
    }
}
