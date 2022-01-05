<?php declare(strict_types=1);
/**
 * This file is part of marijnvanwezel/reflection-file.
 *
 * (c) Marijn van Wezel <marijnvanwezel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReflectionFile\Reflection;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal
 */
class ReflectionVisitor extends NodeVisitorAbstract
{
	private array $classNames = [];
	private array $traitNames = [];
	private array $interfaceNames = [];
	private array $enumNames = [];
	private array $functionNames = [];
	private array $constNames = [];

	public function getClassNames(): array
	{
		return $this->classNames;
	}

	public function getTraitNames(): array
	{
		return $this->traitNames;
	}

	public function getInterfaceNames(): array
	{
		return $this->interfaceNames;
	}

	public function getEnumNames(): array
	{
		return $this->enumNames;
	}

	public function getFunctionNames(): array
	{
		return $this->functionNames;
	}

	public function getConstNames(): array
	{
		return $this->constNames;
	}

	public function enterNode(Node $node): ?int
	{
		if ($node instanceof Namespace_) {
			// If the node is a namespace, we do want to traverse its children
			return null;
		}

		if ($node instanceof Node\Stmt\Class_ && $node->namespacedName !== null) {
			$this->classNames[] = $node->namespacedName->toCodeString();
		} elseif ($node instanceof Node\Stmt\Trait_ && $node->namespacedName !== null) {
			$this->traitNames[] = $node->namespacedName->toCodeString();
		} elseif ($node instanceof Node\Stmt\Interface_ && $node->namespacedName !== null) {
			$this->interfaceNames[] = $node->namespacedName->toCodeString();
		} elseif ($node instanceof Node\Stmt\Enum_ && $node->namespacedName !== null) {
			$this->enumNames[] = $node->namespacedName->toCodeString();
		} elseif ($node instanceof Node\Stmt\Function_ && $node->namespacedName !== null) {
			$this->functionNames[] = $node->namespacedName->toCodeString();
		} elseif ($node instanceof Node\Const_ && $node->namespacedName !== null) {
			$this->constNames[] = $node->namespacedName->toCodeString();
		}

		return NodeTraverser::DONT_TRAVERSE_CHILDREN;
	}
}
