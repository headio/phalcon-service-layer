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

namespace Headio\Phalcon\DomainLayer\Entity;

use DateTime;

/**
 * Entity publishing trait
 */
trait PublishingTrait
{
    /**
     * @Column(type="boolean", nullable=true, column="published")
     */
    protected $published = false;

    /**
     * @Column(type="integer", nullable=true, column="publish_from", length="10")
     */
    protected $publish_from = null;

    /**
     * @Column(type="integer", nullable=true, column="publish_to", length="10")
     */
    protected $publish_to = null;

    /**
     * Get published
     */
    public function getPublished() : bool
    {
        return (bool) $this->published;
    }

    /**
     * Get publish from
     */
    public function getPublishFrom() : ?DateTime
    {
        if (!empty($this->publish_from)) {
            return new DateTime("@{$this->publish_from}");
        }

        return null;
    }

    /**
     * Get publish to
     */
    public function getPublishTo() : ?DateTime
    {
        if (!empty($this->publish_to)) {
            return new DateTime("@{$this->publish_to}");
        }

        return null;
    }

    /**
     * Check whether the model is published.
     */
    public function isPublished() : bool
    {
        if (!$this->getPublished()) {
            return false;
        }
        if ($this->getPublishFrom() instanceof Datetime &&
            $this->getPublishTo() instanceof Datetime) {
            $now = (new DateTime('now'))->getTimestamp();
            if ($now >= $this->getPublishFrom()->getTimestamp() &&
                $now < $this->getPublishTo()->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set published
     */
    public function setPublished(bool $input) : void
    {
        $this->published = $input;
    }

    /**
     * Set publish from
     *
     * @param DateTime|string
     */
    public function setPublishFrom($input) : void
    {
        if ($input instanceof DateTime) {
            $this->publish_from = $input->getTimestamp();
        } elseif (!empty($input)) {
            $instance = new DateTime($input);
            if ($instance instanceof DateTime) {
                $this->publish_from = $instance->getTimestamp();
            }
        } else {
            $this->publish_from = null;
        }
    }

    /**
     * Set publish to
     *
     * @param DateTime|string
     */
    public function setPublishTo($input) : void
    {
        if ($input instanceof DateTime) {
            $this->publish_to = $input->getTimestamp();
        } elseif (!empty($input)) {
            $instance = new DateTime($input);
            if ($instance instanceof DateTime) {
                $this->publish_to = $instance->getTimestamp();
            }
        } else {
            $this->publish_to = null;
        }
    }
}
