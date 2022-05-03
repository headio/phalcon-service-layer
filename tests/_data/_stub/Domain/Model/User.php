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
use Headio\Phalcon\ServiceLayer\Model\Traits\PublishableTrait;
use Headio\Phalcon\ServiceLayer\Model\Traits\TimestampableTrait;
use Headio\Phalcon\ServiceLayer\Model\Behavior\Publishable;
use Headio\Phalcon\ServiceLayer\Model\Behavior\Timestampable;
use Phalcon\Filter\Filter;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Uniqueness;

/**
 * @Source("User")
 *
 * @HasManyToMany(
 *     "id",
 *     "Stub\Domain\Model\RoleUser",
 *     "user_id",
 *     "role_id",
 *     "Stub\Domain\Model\Role",
 *     "id", {
 *         "alias" : "roles",
 *         "params": {
 *             "order" : "[Stub\Domain\Model\Role].[id] DESC"
 *         }
 *     }
 * )
 *
 * @HasMany(
 *     "id",
 *     "Stub\Domain\Model\RoleUser",
 *     "user_id",
 *     {
 *         "alias" : "userRoles"
 *     }
 * )
 */
class User extends Model implements UserInterface
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    protected ?int $id = null;

    /**
     * @Column(type="string", nullable=false, column="name", length="64")
     */
    protected ?string $name = null;

    /**
     * @Column(type="string", nullable=false, column="email", length="84")
     */
    protected ?string $email = null;

    /**
     * Use trait for timestamp functionality.
     */
    use TimestampableTrait;

    /**
     * Use trait for publishing functionality.
     */
    use PublishableTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        self::setup(
            [
                'exceptionOnFailedSave' => true,
            ]
        );

        $this->addBehavior(new Timestampable());
        $this->addBehavior(new Publishable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $val): void
    {
        $this->name = $val;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $val): void
    {
        $this->email = $val;
    }

    /**
     * {@inheritDoc}
     */
    public function validation(): bool
    {
        $validator = new Validation();

        $validator->setFilters(
            [
                'name',
                'email',
            ],
            [
                Filter::FILTER_STRING
            ]
        );

        $validator->setFilters(
            'published',
            FILTER::FILTER_BOOL
        );

        $validator->setFilters(
            'email',
            [
                Filter::FILTER_EMAIL,
                Filter::FILTER_STRIPTAGS
            ]
        );

        $validator->add(
            [
                'name',
                'email',
            ],
            new PresenceOf(
                [
                    'message' => [
                        'name' => 'This field is required.',
                        'email' => 'This field is required.',
                    ]
                ]
            )
        );

        $validator->add(
            'email',
            new Email(
                [
                    'message' => 'The value is not a valid email address.'
                ]
            )
        );

        $validator->add(
            'email',
            new Uniqueness(
                [
                    'message' => 'The email already exists.'
                ]
            )
        );

        return $this->validate($validator);
    }
}
