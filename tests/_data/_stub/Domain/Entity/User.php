<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Entity;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\PublishingTrait;
use Headio\Phalcon\ServiceLayer\Entity\TimestampTrait;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Publishable;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Timestampable;

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
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name", length="64")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="email", length="84")
     */
    public $email;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
