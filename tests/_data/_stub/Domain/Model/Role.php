<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Model;

use Headio\Phalcon\ServiceLayer\Model\AbstractModel as Model;
use Headio\Phalcon\ServiceLayer\Model\Behavior\CacheInvalidateable;
use Headio\Phalcon\ServiceLayer\Model\Behavior\Timestampable;
use Headio\Phalcon\ServiceLayer\Model\Traits\TimestampableTrait;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Filter\Validation\Validator\Uniqueness;

/**
 * @Source("Role")
 *
 * @HasManyToMany(
 *     "id",
 *     "Stub\Domain\Model\RoleUser",
 *     "role_id",
 *     "user_id",
 *     "Stub\Domain\Model\User",
 *     "id",
 *     {
 *         "alias" : "users",
 *         "params": {
 *             "order" : "[Stub\Domain\Model\User].[id] DESC"
 *         }
 *     }
 * )
 *
 * @HasMany(
 *     "id",
 *     "Stub\Domain\Model\RoleUser",
 *     "role_id",
 *     {
 *         "alias" : "roleUsers"
 *     }
 * )
 */
class Role extends Model implements RoleInterface
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
    use TimestampableTrait;

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
                        'Stub\\Domain\\Model\\Role',
                        'Stub\\Domain\\Model\\User'
                    ]
                ]
            )
        );
    }

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
