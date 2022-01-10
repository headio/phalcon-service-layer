<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Entity\Behavior;

use Headio\Phalcon\ServiceLayer\Entity\Behavior\Timestampable;
use Stub\Domain\Entity\User;
use Phalcon\Mvc\Model\BehaviorInterface;
use IntegrationTester;

class TimestampableCest
{
    private User $model;

    private BehaviorInterface $behavior;

    public function _before(IntegrationTester $I)
    {
        $this->model = new User();
        $this->behavior = new Timestampable(
            [
                'foo' => 'bar',
                'beforeValidationOnCreate' => true,
                'beforeValidationOnUpdate' => true,
            ]
        );
    }

    public function behaviorHasOptions(IntegrationTester $I)
    {
        $I->wantToTest('the behavior returns options as an array representation');

        $method = $I->getClassMethod($this->behavior, 'getOptions');
        $result = $method->invoke($this->behavior);

        expect_that(!empty($result));
    }

    public function behaviorHasNoOptions(IntegrationTester $I)
    {
        $I->wantToTest('the behavior returns no options');

        $behavior = new Timestampable();
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);

        expect_that(empty($result));
    }

    public function behaviorHasEventHook(IntegrationTester $I)
    {
        $I->wantToTest('the behavior registers event hooks');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'beforeEvent');

        expect_that(is_bool($result));
    }

    public function behaviorTakesNoAction(IntegrationTester $I)
    {
        $I->wantToTest('the behavior does not take action on event');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'afterDelete');

        expect($result)->equals(false);
    }

    public function behaviorTakesActionOnBeforeValidationOnCreateEventHook(IntegrationTester $I)
    {
        $I->wantToTest('the behavior takes action on `beforeValidationOnCreate` event hook');

        $this->behavior->notify('beforeValidationOnCreate', $this->model);

        expect($this->model->getCreated())->isInstanceOf('DateTime');
        expect($this->model->getModified())->isInstanceOf('DateTime');
    }

    public function behaviorTakesActionOnBeforeValidationOnUpdateEventHook(IntegrationTester $I)
    {
        $I->wantToTest('the behavior takes action on `beforeValidationOnUpdate` event hook');
        $this->behavior->notify('beforeValidationOnUpdate', $this->model);

        expect($this->model->getModified())->isInstanceOf('DateTime');
    }
}
