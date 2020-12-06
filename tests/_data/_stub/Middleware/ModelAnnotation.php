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

namespace Stub\Middleware;

use Phalcon\Events\EventInterface;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Di\Injectable;

/**
 * Middleware component to process entity relationships using annotations.
 */
class ModelAnnotation extends Injectable
{
    /**
     * Process model annotations and pass to modal manager.
     */
    public function afterInitialize(EventInterface $event, ManagerInterface $manager, ModelInterface $model) : string
    {
        $reflector = $this->annotations->get($model);
        $annotations = $reflector->getClassAnnotations();

        if ($annotations) {
            foreach ($annotations as $annotation) {
                switch ($annotation->getName()) {
                    case 'Source':
                        $arguments = $annotation->getArguments();
                        $manager->setModelSource($model, $arguments[0]);
                    break;
                    case 'HasMany':
                        $arguments = $annotation->getArguments();
                        $manager->addHasMany($model, ...$arguments);
                    break;
                    case 'HasManyToMany':
                        $arguments = $annotation->getArguments();
                        $manager->addHasManyToMany($model, ...$arguments);
                    break;
                    case 'HasOne':
                        $arguments = $annotation->getArguments();
                        $manager->addHasOne($model, ...$arguments);
                    break;
                    case 'BelongsTo':
                        $arguments = $annotation->getArguments();
                        $manager->addBelongsTo($model, ...$arguments);
                    break;
                }
            }
        }

        return $event->getType();
    }
}
