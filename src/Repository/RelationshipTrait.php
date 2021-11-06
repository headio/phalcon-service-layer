<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\TransactionInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use function array_diff;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function array_push;
use function get_class;
use function is_null;
use function sprintf;

trait RelationshipTrait
{
    /**
     * Synchronize a "many-to-many" model relationship for a source entity
     * based on a collection of model primary keys; returns true on success
     * and false otherwise.
     *
     * This implementation requires both the @hasMany and @hasManyToMany
     * relationship definitions on the source entity.
     *
     * @throws OutOfRangeException
     */
    public function synchronize(
        string $aliasHasManyToMany,
        string $aliasHasMany,
        EntityInterface $entity,
        array $keys
    ): bool {
        $entityName = get_class($entity);

        /**
         * Stop execution if the entity's "@hasMany" alias is unknown.
         */
        if (false === ($this->modelsManager->getRelationByAlias($entityName, $aliasHasMany))) {
            throw new OutOfRangeException(
                sprintf(
                    "Missing alias '%s' in '%s' entity relationship definition.",
                    $aliasHasMany,
                    $entityName
                )
            );
        }

        /**
         * Stop execution if the entity's "@hasManyToMany" alias is unknown.
         */
        if (false === ($relation = $this->modelsManager->getRelationByAlias(
            $entityName,
            $aliasHasManyToMany
        ))) {
            throw new OutOfRangeException(
                sprintf(
                    "Missing alias '%s' in '%s' entity relationship definition.",
                    $aliasHasManyToMany,
                    $entityName
                )
            );
        }

        /** @var ResultsetInterface */
        $related = $this->getRelated($aliasHasMany, $entity, $this->getQueryFilter());

        /**
         * If no keys are given, unlink the existing
         * models and return the result.
         */
        if ($related->count() <> 0 && empty($keys)) {
            return $related->delete();
        }

        /**
         * Otherwise grab the current related primary keys and calculate the
         * changes necessary to synchronize the relations based on the given
         * primary keys.
         */
        if ($related->count() <> 0) {
            $related->rewind();
            while ($related->valid()) {
                $current[] = $related->current()->{$relation->getIntermediateReferencedFields()};
                $related->next();
            }

            /**
             * Store the required operations
             */
            $link = $ignore = [];

            foreach ($keys as $key) {
                if (array_key_exists($key, array_flip($current))) {
                    array_push($ignore, $key);
                } else {
                    array_push($link, $key);
                }
            }

            $unlink = array_diff($current, array_merge($ignore, $link));

            /**
             * Detach the models
             */
            if (!empty($unlink)) {
                $result = $related->delete(
                    function ($related) use ($relation, $unlink) {
                        if (array_key_exists(
                            $related->{$relation->getIntermediateReferencedFields()},
                            array_flip($unlink)
                        )) {
                            return true;
                        }

                        return false;
                    }
                );
            }

            /**
             * Attach the models
             */
            if (!empty($link)) {
                return $this->link($aliasHasManyToMany, $entity, $link);
            }

            if (!empty($unlink)) {
                return $result;
            }

            return true;
        }

        /**
         * Just assign the new models, as no related models exist.
         */
        if (!empty($keys)) {
            return $this->link($aliasHasManyToMany, $entity, $keys);
        }

        return true;
    }

    /**
     * Associate a collection of models via an alias defined in the
     * source entity relationship definition.
     *
     * @throws OutOfRangeException
     */
    public function link(string $alias, EntityInterface $entity, array $keys): bool
    {
        $entityName = get_class($entity);
        /**
         * Stop execution if the alias is unknown.
         */
        if (false === $this->modelsManager->getRelationByAlias($entityName, $alias)) {
            throw new OutOfRangeException(
                sprintf(
                    "Missing alias '%s' in '%s' entity relationship definition.",
                    $alias,
                    $entityName
                )
            );
        }

        /** @var QueryInterface */
        $query = $this->createCriteria()->inWhere('id', $keys)
            ->createBuilder()
            ->getQuery();

        /** @var ResulsetInterface */
        $models = $query->execute();

        if ($models->count() <> 0) {
            $instances = [];
            $models->rewind();
            while ($models->valid()) {
                $instances[] = $models->current();
                $models->next();
            }

            $entity->{$alias} = $instances;
        }

        return true;
    }

    /**
     * Detach a collection of models via an alias defined in the
     * source entity relationship definition.
     *
     * @note:
     * This implementation only supports detaching "many-to-many" relationships.
     *
     * @throws OutOfRangeException
     */
    public function unlink(string $alias, EntityInterface $entity, array $keys, ?TransactionInterface $transaction = null): bool
    {
        /**
         * Nothing to process continue!
         */
        if (empty($keys)) {
            return true;
        }

        $entityName = get_class($entity);
        /**
         * Stop execution if the alias is unknown.
         */
        if (false === ($relation = $this->modelsManager->getRelationByAlias($entityName, $alias))) {
            throw new OutOfRangeException(
                sprintf(
                    "Missing alias '%s' in '%s' entity relationship definition.",
                    $alias,
                    $entityName
                )
            );
        }
        /**
         * Only supports detaching @hasManyToMany relationships!
         */
        if (!$relation->isThrough()) {
            throw new OutOfRangeException(
                'This implementation only support detaching relationships in a many-to-many relationship definition.'
            );
        }

        $phql = <<<EX
DELETE FROM {$relation->getIntermediateModel()} 
WHERE {$relation->getIntermediateFields()} = :{$relation->getIntermediateFields()}:
AND {$relation->getIntermediateReferencedFields()} IN ({keys:array})
EX;
        $query = $this->modelsManager->createQuery($phql)
            ->setBindParams(
                [
                    $relation->getIntermediateFields() => $entity->getId(),
                    'keys' => $keys
                ]
            )
            ->setBindTypes(
                [
                    $relation->getIntermediateFields() => Column::BIND_PARAM_INT,
                    'keys' => Column::BIND_PARAM_INT
                ]
            );

        if (!is_null($transaction)) {
            $query->setTransaction($transaction);
        }

        $result = $query->execute();

        return $result->success();
    }

    /**
     * Return an instance of the query criteria pre-populated
     * with the entity managed by this repository.
     */
    abstract public function createCriteria(): CriteriaInterface;

    /**
     * Return the related models from cache or storage.
     */
    abstract public function getRelated(string $alias, EntityInterface $entity, FilterInterface $filter): ResultsetInterface;
}
