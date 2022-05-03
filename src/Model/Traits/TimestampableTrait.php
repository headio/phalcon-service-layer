<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model\Traits;

use DateTimeImmutable;

trait TimestampableTrait
{
    /**
     * @Column(type="integer", nullable=false, column="created")
     */
    protected ?int $created = null;

    /**
     * @Column(type="integer", nullable=false, column="modified")
     */
    protected ?int $modified = null;

    /**
     * Get created
     */
    public function getCreated(): DateTimeImmutable
    {
        return new DateTimeImmutable("@{$this->created}");
    }

    public function getModified(): DateTimeImmutable
    {
        return new DateTimeImmutable("@{$this->modified}");
    }

    public function setCreated(DateTimeImmutable $input): void
    {
        $this->created = $input->getTimestamp();
    }

    public function setModified(DateTimeImmutable $input): void
    {
        $this->modified = $input->getTimestamp();
    }
}
