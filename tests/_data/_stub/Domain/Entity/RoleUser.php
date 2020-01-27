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

/**
 * @Source("Role_User")
 *
 * @BelongsTo(
 *     "role_id",
 *     "Stub\Domain\Entity\Role",
 *     "id",
 *     {
 *         "alias" : "role"
 *     }
 * )
 *
 * @BelongsTo(
 *     "user_id",
 *     "Stub\Domain\Entity\User",
 *     "id",
 *     {
 *         "alias" : "user"
 *     }
 * )
 */
class RoleUser extends AbstractEntity
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="role_id", length="5")
     */
    public $role_id;

    /**
     * @Column(type="integer", nullable=false, column="user_id", length="10")
     */
    public $user_id;
}
