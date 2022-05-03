<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

interface Condition
{
    public const AND = 'AND';

    public const OR = 'OR';

    public const EQUAL = '=';

    public const GREATER_THAN = '>';

    public const GREATER_THAN_OR_EQUAL = '>=';

    public const IN = 'IN';

    public const NOT_IN = 'NOT IN';

    public const LESS_THAN = '<';

    public const LESS_THAN_OR_EQUAL = '<=';

    public const LIKE = 'LIKE';

    public const NOT_LIKE = 'NOT LIKE';

    public const NOT_EQUAL = '<>';

    public const IS_NULL = 'IS NULL';

    public const IS_NOT_NULL = 'IS NOT NULL';
}
