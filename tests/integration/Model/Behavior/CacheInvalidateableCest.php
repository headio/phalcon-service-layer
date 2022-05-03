<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Model\Behavior;

use Headio\Phalcon\ServiceLayer\Model\Behavior\CacheInvalidateable;
use Stub\Domain\Model\User as Model;
use Stub\Service\CacheManager;
use Phalcon\Di\FactoryDefault;
use IntegrationTester;

class CacheInvalidateableCest
{
    private $behavior;

    public function _before(IntegrationTester $I)
    {
        $this->behavior = new CacheInvalidateable(
            [
                'invalidate' => [
                    Model::class
                ]
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

        $behavior = new CacheInvalidateable();
        $method = $I->getClassMethod($behavior, 'getOptions');
        $result = $method->invoke($behavior);

        expect_that(empty($result));
    }

    public function behaviorTakesNoAction(IntegrationTester $I)
    {
        $I->wantToTest('the behavior does not take action on event');

        $method = $I->getClassMethod($this->behavior, 'mustTakeAction');
        $result = $method->invoke($this->behavior, 'afterDelete');

        expect($result)->equals(false);
    }

    public function behaviorTakesActionOnRegisteredEventHook(IntegrationTester $I)
    {
        $I->wantToTest('the behavior does take action on event');

        $behavior = new CacheInvalidateable(
            [
                'afterDelete' => [
                    'invalidate' => [
                        Model::class
                    ]
                ]
            ]
        );
        $method = $I->getClassMethod($behavior, 'mustTakeAction');
        $result = $method->invoke($behavior, 'afterDelete');

        expect_that(is_bool($result));
    }
}
