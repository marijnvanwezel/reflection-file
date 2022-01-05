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

use ParseError;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

/**
 * @internal
 */
class SourceReflector
{
	public readonly array $classNames;
	public readonly array $traitNames;
	public readonly array $interfaceNames;
	public readonly array $enumNames;
	public readonly array $functionNames;
	public readonly array $constNames;

	/**
	 * @param string $source The source to provide reflection about
	 */
	public function __construct(string $source)
	{
		try {
			// This parser counterintuitively also supports >=PHP 8.0
			$parserFactory = new ParserFactory();
			$statements = $parserFactory->create(ParserFactory::ONLY_PHP7)->parse($source);
		} catch (Error $error) {
			throw new ParseError('The source could not be parsed', 0, $error);
		}

		$reflectionVisitor = new ReflectionVisitor();
		$traverser = new NodeTraverser();

		$traverser->addVisitor(new NameResolver());
		$traverser->addVisitor($reflectionVisitor);
		$traverser->traverse($statements);

		$this->constNames = $reflectionVisitor->getConstNames();
		$this->enumNames = $reflectionVisitor->getEnumNames();
		$this->interfaceNames = $reflectionVisitor->getInterfaceNames();
		$this->traitNames = $reflectionVisitor->getTraitNames();
		$this->classNames = $reflectionVisitor->getClassNames();
		$this->functionNames = $reflectionVisitor->getFunctionNames();
	}
}
