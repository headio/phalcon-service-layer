<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Model;

use Headio\Phalcon\ServiceLayer\Model\ModelInterface;

interface TagInterface extends ModelInterface
{
    public function getId(): ?int;

    public function getLabel(): ?string;
}
