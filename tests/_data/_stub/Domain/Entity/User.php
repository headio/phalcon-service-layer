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

namespace Stub\Domain\Entity;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\PublishingTrait;
use Headio\Phalcon\ServiceLayer\Entity\TimestampTrait;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Publishable;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Timestampable;
use Phalcon\Filter;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * @Source("User")
 *
 * @HasManyToMany(
 *     "id",
 *     "Stub\Domain\Entity\RoleUser",
 *     "user_id",
 *     "role_id",
 *     "Stub\Domain\Entity\Role",
 *     "id", {
 *         "alias" : "roles",
 *         "params": {
 *             "order" : "[Stub\Domain\Entity\Role].[id] DESC"
 *         }
 *     }
 * )
 *
 * @HasMany(
 *     "id",
 *     "Stub\Domain\Entity\RoleUser",
 *     "user_id",
 *     {
 *         "alias" : "userRoles"
 *     }
 * )
 */
class User extends AbstractEntity
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    protected $id;

    /**
     * @Column(type="string", nullable=false, column="name", length="64")
     */
    protected $name;

    /**
     * @Column(type="string", nullable=false, column="email", length="84")
     */
    protected $email;

    /**
     * Use trait for timestamp functionality.
     */
    use TimestampTrait;

    /**
     * Use trait for publishing functionality.
     */
    use PublishingTrait;

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

    public function getId(): int
    {
        return (int) $this->id;
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
