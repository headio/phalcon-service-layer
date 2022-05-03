<?php
/**
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Model;

use Headio\Phalcon\ServiceLayer\Model\AbstractModel as Model;

/**
 * @Source("Tag")
 */
class Tag extends Model implements TagInterface
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="5")
     */
    protected ?int $id = null;

    /**
     * @Column(type="string", nullable=false, column="label", length="64")
     */
    protected ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $val): void
    {
        $this->label = $val;
    }
}
