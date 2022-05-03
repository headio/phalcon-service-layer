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

/**
 * @Source("Role_User")
 *
 * @BelongsTo(
 *     "role_id",
 *     "Stub\Domain\Model\Role",
 *     "id",
 *     {
 *         "alias" : "role"
 *     }
 * )
 *
 * @BelongsTo(
 *     "user_id",
 *     "Stub\Domain\Model\User",
 *     "id",
 *     {
 *         "alias" : "user"
 *     }
 * )
 */
class RoleUser extends Model
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    public ?int $id = null;

    /**
     * @Column(type="integer", nullable=false, column="role_id", length="5")
     */
    public ?int $role_id = null;

    /**
     * @Column(type="integer", nullable=false, column="user_id", length="10")
     */
    public ?int $user_id = null;
}
