<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Entity\Behavior;

use Headio\Phalcon\ServiceLayer\Entity\Behavior\Publishable;
use Stub\Domain\Entity\User as Entity;
use IntegrationTester;

class PublishableCest
{
    private $entity;

    private $behavior;

    public function _before(IntegrationTester $I)
    {
        $this->entity = new Entity();
        $this->behavior = new Publishable(
            [
                'foo' => 'bar',
                'beforeSave' => true,
            ]
        );
    }

    public function behaviorHasOptions(IntegrationTester $I)
    {
        $I->wantToTest('Behavior returns options as an array representation');

        $method = $I->getClassMethod($this->behavior, 'getOptions');
        $result = $method->invoke($this->behavior);

        expect_that(!empty($result));
    }

    public function behaviorHasNoOptions(IntegrationTester $I)
    {
        $I->wantToTest('Behavior returns no options');

        $behavior = new Publishable();
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);

        expect_that(empty($result));
    }

    public function behaviorHasEventHook(IntegrationTester $I)
    {
        $I->wantToTest('Behavior registers event hooks');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'beforeEvent');

        expect_that(is_bool($result));
    }

    public function behaviorTakesNoAction(IntegrationTester $I)
    {
        $I->wantToTest('Behavior does not take action on event');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'afterDelete');

        expect($result)->equals(false);
    }

    public function behaviorTakesActionOnBeforeSaveEventHook(IntegrationTester $I)
    {
        $I->wantToTest('Behavior takes action on `beforeSave` event hook');

        $this->entity->setPublished(true);
        $this->behavior->notify('beforeSave', $this->entity);

        expect($this->entity->getPublishFrom())->isInstanceOf('DateTime');
        expect($this->entity->getPublishTo())->isInstanceOf('DateTime');

        $this->entity->setPublished(false);
        $this->behavior->notify('beforeSave', $this->entity);

        expect_that(is_null($this->entity->getPublishFrom()));
        expect_that(is_null($this->entity->getPublishTo()));
    }
}
