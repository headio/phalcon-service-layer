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

class Tag extends Filter
{
    private string $label;

    private string $keyword;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $val): FilterInterface
    {
        $this->label = $val;
        $this->eq('label', $val);

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $val): FilterInterface
    {
        $this->keyword = $val;
        $this->like('label', $val);

        return $this;
    }
}
