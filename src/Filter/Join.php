<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

class Join implements JoinInterface
{
    private ?string $alias;

    private string $entity;

    private ?string $constraint;

    private string $type;

    public function __construct(string $entity, ?string $constraint, string $type, ?string $alias = null)
    {
        $this->entity = $entity;
        $this->constraint = $constraint;
        $this->alias = $alias;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstraint(): ?string
    {
        return $this->constraint;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return $this->type;
    }
}
