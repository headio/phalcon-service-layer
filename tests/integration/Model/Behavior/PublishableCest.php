<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Model\Behavior;

use Headio\Phalcon\ServiceLayer\Model\Behavior\Publishable;
use Stub\Domain\Model\User;
use Phalcon\Mvc\Model\BehaviorInterface;
use IntegrationTester;
use DateTimeImmutable;

class PublishableCest
{
    private User $model;

    private BehaviorInterface $behavior;

    public function _before(IntegrationTester $I)
    {
        $this->model = new User();
        $this->behavior = new Publishable(
            [
                'foo' => 'bar',
                'beforeSave' => true,
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

        $behavior = new Publishable();
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);

        expect_that(empty($result));
    }

    public function behaviorHasExpiryOption(IntegrationTester $I)
    {
        $I->wantToTest('the behavior has expiry option');

        $behavior = new Publishable(['expiry' => '+3 months']);
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);
        expect_that(!empty($result));
    }

    public function behaviorGetExpiryOption(IntegrationTester $I)
    {
        $I->wantToTest('the behavior returns expiry option');
        $definition = ['expiry' => '+3 months'];
        $behavior = new Publishable($definition);
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);
        expect($result['expiry'])->equals($definition['expiry']);
    }

    public function behaviorHandlesCustomExpiryOption(IntegrationTester $I)
    {
        $I->wantToTest('the behavior handles custom expiry option');
        $now = new DateTimeImmutable('now');
        $behavior = new Publishable(['expiry' => '+3 months']);
        $this->model->setPublished(true);
        $behavior->notify('beforeSave', $this->model);

        expect($this->model->getPublishFrom())->isInstanceOf('DateTimeImmutable');
        expect($this->model->getPublishTo())->isInstanceOf('DateTimeImmutable');
        expect(
            $now->getTimestamp() < $this->model->getPublishTo()->getTimestamp()
        )->true();
        expect(
            $now->modify('+4 months')->getTimestamp() < $this->model->getPublishTo()->getTimestamp()
        )->false();
    }

    public function behaviorHandlesDefaultExpiryOption(IntegrationTester $I)
    {
        $I->wantToTest('the behavior handles default expiry option');
        $now = new DateTimeImmutable('now');
        $behavior = new Publishable();
        $this->model->setPublished(true);
        $behavior->notify('beforeSave', $this->model);

        expect($this->model->getPublishFrom())->isInstanceOf('DateTimeImmutable');
        expect($this->model->getPublishTo())->isInstanceOf('DateTimeImmutable');
        expect(
            $now->getTimestamp() < $this->model->getPublishTo()->getTimestamp()
        )->true();
        expect(
            $now->modify('+11 years')->getTimestamp() < $this->model->getPublishTo()->getTimestamp()
        )->false();
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

    public function behaviorTakesActionOnBeforeSaveEventHook(IntegrationTester $I)
    {
        $I->wantToTest('the behavior takes action on `beforeSave` event hook');

        $this->model->setPublished(true);
        $this->behavior->notify('beforeSave', $this->model);

        expect($this->model->getPublishFrom())->isInstanceOf('DateTimeImmutable');
        expect($this->model->getPublishTo())->isInstanceOf('DateTimeImmutable');

        $this->model->setPublished(false);
        $this->behavior->notify('beforeSave', $this->model);

        expect_that(is_null($this->model->getPublishFrom()));
        expect_that(is_null($this->model->getPublishTo()));
    }
}
