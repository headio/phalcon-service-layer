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

use Headio\Phalcon\DomainLayer\Filter\FilterInterface;
use Headio\Phalcon\DomainLayer\Repository\RelationshipTrait;
use Headio\Phalcon\DomainLayer\Repository\QueryRepository;
use Stub\Domain\Filter\User as Filter;

class User extends QueryRepository implements UserInterface
{
    use RelationshipTrait;

    public function getEntityName() : string
    {
        return 'Stub\\Domain\\Entity\\User';
    }

    public function getQueryFilter() : FilterInterface
    {
        return new Filter();
    }
}
