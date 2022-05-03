<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Repository;

use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Headio\Phalcon\ServiceLayer\Repository\Traits\CacheableTrait;
use Headio\Phalcon\ServiceLayer\Repository\Traits\RelationshipTrait;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

class CacheableRole extends QueryRepository implements RoleInterface, EventsAwareInterface
{
    use CacheableTrait;

    use RelationshipTrait;

    public function newInstance(): ModelInterface
    {
        $modelName = $this->getModel();
        $model = new $modelName();

        return $model;
    }

    protected function getModelName(): string
    {
        return 'Stub\\Domain\\Model\\Role';
    }
}
