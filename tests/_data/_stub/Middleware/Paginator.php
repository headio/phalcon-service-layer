<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Middleware;

use Headio\Phalcon\ServiceLayer\Exception\OutOfBoundsException;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\Query;
use Phalcon\Events\EventInterface;
use Phalcon\Mvc\DispatcherInterface;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Filter\Filter;

/**
 * Middleware component to pre-process pagination for
 * action controllers.
 *
 * @property \Phalcon\Annotations\Adapter\AdapterInterface $annotations
 */
class Paginator extends AbstractInjectionAware
{
    /**
     * Before dispatch loop event processing hook.
     */
    public function beforeDispatchLoop(EventInterface $event, DispatcherInterface $dispatcher): void
    {
        $annotations = $dispatcher->getDI()->get('annotations')->getMethod(
            $dispatcher->getControllerClass(),
            $dispatcher->getActiveMethod()
        );

        if ($annotations->has('Paginateable')) {
            /** @var \Phalcon\Annotations\Annotation */
            $annotation = $annotations->get('Paginateable');
            // Paginateable action controller
            if (true === $annotation->getArguments()[0]) {
                /** @var \Phalcon\Config\ConfigInterface */
                $config = $dispatcher->getDI()->get('config');
                /** @var \Phalcon\Config\ConfigInterface|bool */
                $options = $config->path('paginator.cursor.queryIdentifiers', false);

                if (!is_iterable($options)) {
                    throw new OutOfBoundsException('Missing cursor-based query identifier settings');
                }

                if ($dispatcher->hasParam('paging')) {
                    $query = new Query(
                        $dispatcher->getParam('cursor', Filter::FILTER_ABSINT),
                        $dispatcher->getParam('paging') === $options->get('before'),
                        $dispatcher->getParam('paging') === $options->get('after')
                    );
                } else {
                    $query = new Query(0, false, false);
                }

                $dispatcher->setParams([$query]);
            }
        }
    }
}
