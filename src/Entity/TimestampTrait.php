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

namespace Headio\Phalcon\ServiceLayer\Entity;

use DateTime;

/**
 * Entity timestamp trait
 */
trait TimestampTrait
{
    /**
     * @Column(type="integer", nullable=false, column="created", length="10")
     */
    protected $created;

    /**
     * @Column(type="integer", nullable=false, column="modified", length="10")
     */
    protected $modified;

    /**
     * Get created
     */
    public function getCreated() : DateTime
    {
        return new DateTime("@{$this->created}");
    }

    /**
     * Get last modified
     */
    public function getModified()  : DateTime
    {
        return new DateTime("@{$this->modified}");
    }

    /**
     * Set created
     */
    public function setCreated(DateTime $input) : void
    {
        $this->created = $input->getTimestamp();
    }

    /**
     * Set modified
     */
    public function setModified(DateTime $input) : void
    {
        $this->modified = $input->getTimestamp();
    }
}
