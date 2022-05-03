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

trait PublishableTrait
{
    /**
     * @Column(type="boolean", nullable=true, column="published")
     */
    protected ?bool $published = false;

    /**
     * @Column(type="integer", nullable=true, column="publish_from")
     */
    protected ?int $publish_from = null;

    /**
     * @Column(type="integer", nullable=true, column="publish_to")
     */
    protected ?int $publish_to = null;

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function getPublishFrom(): ?DateTimeImmutable
    {
        if (!empty($this->publish_from)) {
            return new DateTimeImmutable("@{$this->publish_from}");
        }

        return null;
    }

    public function getPublishTo(): ?DateTimeImmutable
    {
        if (!empty($this->publish_to)) {
            return new DateTimeImmutable("@{$this->publish_to}");
        }

        return null;
    }

    /**
     * Check whether the model is published; the model
     * is considered published if the date time lies
     * between the "from" and "to" date definitions.
     */
    public function isPublished(): bool
    {
        if (!$this->getPublished()) {
            return false;
        }
        if ($this->getPublishFrom() instanceof DateTimeImmutable &&
            $this->getPublishTo() instanceof DateTimeImmutable) {
            $now = (new DateTimeImmutable('now'))->getTimestamp();
            if ($now >= $this->getPublishFrom()->getTimestamp() &&
                $now < $this->getPublishTo()->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    public function setPublished(bool $input): void
    {
        $this->published = $input;
    }

    public function setPublishFrom(DateTimeImmutable|string|null $input): void
    {
        if ($input instanceof DateTimeImmutable) {
            $this->publish_from = $input->getTimestamp();
        } elseif (!empty($input)) {
            $instance = new DateTimeImmutable($input);
            if ($instance instanceof DateTimeImmutable) {
                $this->publish_from = $instance->getTimestamp();
            }
        } else {
            $this->publish_from = null;
        }
    }

    public function setPublishTo(DateTimeImmutable|string|null $input): void
    {
        if ($input instanceof DateTimeImmutable) {
            $this->publish_to = $input->getTimestamp();
        } elseif (!empty($input)) {
            $instance = new DateTimeImmutable($input);
            if ($instance instanceof DateTimeImmutable) {
                $this->publish_to = $instance->getTimestamp();
            }
        } else {
            $this->publish_to = null;
        }
    }
}
