<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Entity\Behavior;

use Headio\Phalcon\ServiceLayer\Entity\Behavior\CacheInvalidateable;
use Stub\Domain\Entity\User as Entity;
use IntegrationTester;

class CacheInvalidateableCest
{
    private $behavior;

    public function _before(IntegrationTester $I)
    {
        $this->behavior = new CacheInvalidateable(
            [
                'invalidate' => [
                    Entity::class
                ]
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

        $behavior = new CacheInvalidateable();
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);

        expect_that(empty($result));
    }

    public function behaviorTakesNoAction(IntegrationTester $I)
    {
        $I->wantToTest('Behavior does not take action on event');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'afterDelete');

        expect($result)->equals(false);
    }

    public function behaviorTakesActionOnRegisteredEventHook(IntegrationTester $I)
    {
        $I->wantToTest('Behavior does take action on event');

        $behavior = new CacheInvalidateable(
            [
                'afterDelete' => [
                    'invalidate' => [
                        Entity::class
                    ]
                ]
            ]
        );
        $method = $I->getClassMethod($behavior, 'mustTakeAction');
        $result = $method->invoke($behavior, 'afterDelete');

        expect_that(is_bool($result));
    }
}
