<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Condition;
use Headio\Phalcon\ServiceLayer\Filter\Filter;
use IntegrationTester;

class ConditionCest
{
    public function canCreateConditionConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a condition constaint');
        $args = [
            'column' => 'name',
            'value' => 'abc',
            'operator' => Filter::EQUAL,
        ];
        $cond = new Condition(
            ...array_values($args)
        );

        expect($cond->getColumn())->equals($args['column']);
        expect($cond->getValue())->equals($args['value']);
        expect($cond->getType())->equals(Condition::AND);
        expect($cond->getOperator())->equals($args['operator']);
    }

    public function canCreateAndConditionConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a condition constaint of type `and`');
        $args = [
            'column' => 'name',
            'value' => 'abc',
            'operator' => Filter::EQUAL,
            'type' => 'AND',
        ];
        $cond = new Condition(
            ...array_values($args)
        );

        expect($cond->getColumn())->equals($args['column']);
        expect($cond->getValue())->equals($args['value']);
        expect($cond->getType())->equals(Condition::AND);
        expect($cond->getOperator())->equals($args['operator']);
    }

    public function canCreateOrConditionConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a condition constaint of type `or`');
        $args = [
            'column' => 'name',
            'value' => 'abc',
            'operator' => Filter::EQUAL,
            'type' => 'OR',
        ];
        $cond = new Condition(
            ...array_values($args)
        );

        expect($cond->getColumn())->equals($args['column']);
        expect($cond->getValue())->equals($args['value']);
        expect($cond->getType())->equals(Condition::OR);
        expect($cond->getOperator())->equals($args['operator']);
    }
}
