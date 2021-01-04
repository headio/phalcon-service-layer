<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Filter;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;

class Role extends Filter
{
    private ?string $keyword = null;

    private ?int $primaryKey = null;

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $val): FilterInterface
    {
        $this->keyword = $val;

        return $this->like('label', $val);
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
