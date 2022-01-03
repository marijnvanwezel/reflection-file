<?php declare(strict_types=1);
/**
 * This file is part of marijnvanwezel/reflection-file.
 *
 * (c) Marijn van Wezel <marijnvanwezel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReflectionFile;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * This class walks the AST of a PHP script and compiles a list of fully qualified class names of all the classes that
 * were declared in that script.
 *
 * @internal
 */
class FQCNVisitor extends NodeVisitorAbstract
{
	/**
	 * @var string[] The FQCNs of the traversed classes.
	 */
	private array $classNames = [];

	/**
	 * @var ?Name The current namespace, or NULL for the global namespace.
	 */
	private ?Name $namespace;

	/**
	 * Gets the FQCNs of the traversed classes.
	 *
	 * @return string[] The FQCNs of the traversed classes.
	 */
	public function getClassNames(): array
	{
		return $this->classNames;
	}

	/**
	 * @inheritDoc
	 */
	public function beforeTraverse(array $nodes)
	{
		// Start at the global namespace
		$this->startNamespace();
	}

	/**
	 * Start a new namespace.
	 *
	 * @param Name|null $namespace The name of the namespace, or NULL for the global namespace
	 * @return void
	 */
	private function startNamespace(?Name $namespace = null): void
	{
		$this->namespace = $namespace;
	}

	/**
	 * @inheritDoc
	 */
	public function enterNode(Node $node): ?int
	{
		if ($node instanceof Namespace_) {
			$this->startNamespace($node->name);

			// Traverse the statements in the namespace
			return null;
		}

		if ($node instanceof Class_) {
			// Check if we have a name (which may not be the case for anonymous classes)
			if ($node->name !== null) {
				// Add the fully qualified name to the list of classes we've found
				$this->classNames[] = $this->getFQCN($node->name->name)->toCodeString();
			}
		}

		// Namespaces cannot be nested, so we only need to traverse top level children
		return NodeTraverser::DONT_TRAVERSE_CHILDREN;
	}

	/**
	 * Returns the fully qualified class name of the given non-namespaced class identifier.
	 *
	 * @param string $className
	 * @return Name
	 */
	private function getFQCN(string $className): Name
	{
		return FullyQualified::concat($this->namespace, $className);
	}
}
