<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Entity;

use Headio\Phalcon\ServiceLayer\Entity\Behavior\CacheInvalidateable;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Timestampable;
use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\TimestampTrait;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * @Source("Role")
 *
 * @HasManyToMany(
 *     "id",
 *     "Stub\Domain\Entity\RoleUser",
 *     "role_id",
 *     "user_id",
 *     "Stub\Domain\Entity\User",
 *     "id",
 *     {
 *         "alias" : "users",
 *         "params": {
 *             "order" : "[Stub\Domain\Entity\User].[id] DESC"
 *         }
 *     }
 * )
 *
 * @HasMany(
 *     "id",
 *     "Stub\Domain\Entity\RoleUser",
 *     "role_id",
 *     {
 *         "alias" : "roleUsers"
 *     }
 * )
 */
class Role extends AbstractEntity
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    protected ?int $id = null;

    /**
     * @Column(type="string", nullable=false, column="label", length="64")
     */
    protected ?string $label = null;

    /**
     * Use trait for timestamp functionality.
     */
    use TimestampTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->addBehavior(new Timestampable());
        $this->addBehavior(
            new CacheInvalidateable(
                [
                    'invalidate' => [
                        'Stub\\Domain\\Entity\\Role',
                        'Stub\\Domain\\Entity\\User'
                    ]
                ]
            )
        );
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $val): void
    {
        $this->label = $val;
    }

    /**
     * {@inheritDoc}
     */
    public function validation(): bool
    {
        $validator = new Validation();
        $validator->add(
            'label',
            new PresenceOf(
                [
                    'message' => 'This field is required.'
                ]
            )
        );

        $validator->add(
            'label',
            new Uniqueness(
                [
                    'message' => 'This record already exists.'
                ]
            )
        );

        $validator->add(
            'label',
            new StringLength(
                [
                    'max' => 64,
                    'message' => sprintf('The value exceeds the maximum length of %d characters.', 64),
                ]
            )
        );

        return $this->validate($validator);
    }
}
