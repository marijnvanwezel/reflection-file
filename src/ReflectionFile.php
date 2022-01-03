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

use ParseError;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionException;
use SplFileInfo;
use Stringable;

/**
 * The ReflectionFile class reports information about a file.
 */
class ReflectionFile implements Stringable
{
	/**
	 * @var SplFileInfo The reflected file.
	 */
	private SplFileInfo $file;

	/**
	 * @var string The source of the reflected file.
	 */
	private string $source;

	/**
	 * @var string[] The class names declared in the reflected file.
	 */
	private array $declaredClassnames;

	/**
	 * Constructs a new ReflectionFile object.
	 *
	 * @param string|SplFileInfo $fileOrPath Either a string containing the name of the file to reflect, or the
	 * SplFileInfo of the file to reflect.
	 * @throws ReflectionException When the file to reflect does not exist.
	 */
	public function __construct(string|SplFileInfo $fileOrPath)
	{
		$file = is_string($fileOrPath) ? new SplFileInfo($fileOrPath) : $fileOrPath;

		if (!$file->isFile()) {
			throw new ReflectionException('File "' . $fileOrPath . '" does not exist');
		}

		$source = @file_get_contents($file->getPathname());

		if ($source === false) {
			throw new ReflectionException('File "' . $fileOrPath . '" could not be read');
		}

		$this->file = $file;
		$this->source = $source;
	}

	/**
	 * Gets the filename of the file.
	 *
	 * @return string The filename.
	 */
	public function getFileName(): string
	{
		return $this->file->getFilename();
	}

	/**
	 * Gets the source of the file.
	 *
	 * @return string The source of the file.
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * Gets an array of declared class names from the file.
	 *
	 * This class extracts the FQCNs from the file's source code by parsing it. This guarantees that the FQCNs returned
	 * by this function are actually declared in the file, and are not just loaded by the file, unlike more naive
	 * approaches such as using get_declared_classes().
	 *
	 * @return string[] An array of declared class names.
	 *
	 * @throws ParseError When the file could not be parsed.
	 */
	public function getDeclaredClassnames(): array
	{
		if (!isset($this->declaredClassnames)) {
			try {
				// This parser counterintuitively also supports >=PHP 8.0
				$statements = (new ParserFactory())->create(ParserFactory::ONLY_PHP7)->parse($this->source);
			} catch (Error $error) {
				throw new ParseError('File "' . $this->file->getPathname() .
					'" could not be parsed', 0, $error);
			}

			$visitor = new FQCNVisitor();

			$traverser = new NodeTraverser();
			$traverser->addVisitor($visitor);
			$traverser->traverse($statements);

			$this->declaredClassnames = $visitor->getClassNames();
		}

		return $this->declaredClassnames;
	}

	/**
	 * Gets the string representation of this class.
	 *
	 * @return string The string representation (source) of the file.
	 */
	public function __toString(): string
	{
		return $this->source;
	}
}
