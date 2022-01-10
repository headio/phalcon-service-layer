<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Entity;

use Phalcon\Mvc\ModelInterface;

interface UserInterface extends ModelInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getEmail(): ?string;
}
