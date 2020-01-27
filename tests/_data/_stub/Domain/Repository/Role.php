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

namespace Stub\Domain\Repository;

use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Headio\Phalcon\ServiceLayer\Repository\RelationshipTrait;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Stub\Domain\Filter\Role as Filter;
use Stub\Domain\Repository\RoleInterface;

class Role extends QueryRepository implements RoleInterface
{
    use RelationshipTrait;

    public function getEntityName() : string
    {
        return 'Stub\\Domain\\Entity\\Role';
    }

    public function getQueryFilter() : FilterInterface
    {
        return new Filter();
    }
}
