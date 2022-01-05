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
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionFile\Reflection\SourceReflector;
use ReflectionFunction;
use SplFileInfo;
use Stringable;

/**
 * The ReflectionFile class reports information about a file.
 */
class ReflectionFile implements Stringable
{
	/**
	 * @var string The source of the reflected file.
	 */
	private string $source;

	/**
	 * @var string The name of the file
	 */
	private string $fileName;

	/**
	 * @var string The pathname of the file.
	 */
	private string $pathName;

	/**
	 * @var SourceReflector Contains information about the source of the file
	 */
	private SourceReflector $reflector;

	/**
	 * Constructs a new ReflectionFile object.
	 *
	 * @param string|SplFileInfo $fileOrPath Either a string containing the name of the file to reflect, or the
	 * SplFileInfo of the file to reflect.
	 *
	 * @throws ReflectionException When the file to reflect does not exist.
	 * @throws ParseError When the file cannot not be parsed.
	 */
	public function __construct(string|SplFileInfo $fileOrPath)
	{
		$file = is_string($fileOrPath) ? new SplFileInfo($fileOrPath) : $fileOrPath;

		if (!$file->isFile()) {
			throw new ReflectionException('File "' . $fileOrPath . '" does not exist');
		}

		if (!$file->isReadable()) {
			throw new ReflectionException('File "' . $fileOrPath . '" could not be read');
		}

		$this->fileName = $file->getFilename();
		$this->pathName = $file->getPathname();

		$this->source = file_get_contents($this->pathName);
		$this->reflector = new SourceReflector($this->source);
	}

	/**
	 * Gets an array of declared class names from the file.
	 *
	 * @return string[] An array of declared class names.
	 */
	public function getClassNames(): array
	{
		return $this->reflector->classNames;
	}

	/**
	 * Gets an array of classes declared in the file.
	 *
	 * @return ReflectionClass[] An array of declared classes.
	 * @throws ReflectionException When the reflection fails, for instance when the file is not included.
	 */
	public function getClasses(): array
	{
		return array_map(function (string $name): ReflectionClass {
			return new ReflectionClass($name);
		}, $this->reflector->classNames);
	}

	/**
	 * Gets an array of declared trait names from the file.
	 *
	 * @return string[] An array of declared trait names.
	 */
	public function getTraitNames(): array
	{
		return $this->reflector->traitNames;
	}

	/**
	 * Gets an array of traits declared in the file.
	 *
	 * @return ReflectionClass[] An array of declared traits.
	 * @throws ReflectionException When the reflection fails, for instance when the file is not included.
	 */
	public function getTraits(): array
	{
		return array_map(function (string $name): ReflectionClass {
			return new ReflectionClass($name);
		}, $this->reflector->traitNames);
	}

	/**
	 * Gets an array of declared interface names from the file.
	 *
	 * @return string[] An array of declared interface names.
	 */
	public function getInterfaceNames(): array
	{
		return $this->reflector->interfaceNames;
	}

	/**
	 * Gets an array of interfaces declared in the file.
	 *
	 * @return ReflectionClass[] An array of declared interfaces.
	 * @throws ReflectionException When the reflection fails, for instance when the file is not included.
	 */
	public function getInterfaces(): array
	{
		return array_map(function (string $name): ReflectionClass {
			return new ReflectionClass($name);
		}, $this->reflector->interfaceNames);
	}

	/**
	 * Gets an array of declared enum names from the file.
	 *
	 * @return string[] An array of declared enum names.
	 */
	public function getEnumNames(): array
	{
		return $this->reflector->enumNames;
	}

	/**
	 * Gets an array of enums declared in the file.
	 *
	 * @return ReflectionEnum[] An array of declared enums.
	 * @throws ReflectionException When the reflection fails, for instance when the file is not included.
	 */
	public function getEnums(): array
	{
		return array_map(function (string $name): ReflectionEnum {
			return new ReflectionEnum($name);
		}, $this->reflector->enumNames);
	}

	/**
	 * Gets an array of declared function names from the file.
	 *
	 * @return string[] An array of declared function names.
	 */
	public function getFunctionNames(): array
	{
		return $this->reflector->functionNames;
	}

	/**
	 * Gets an array of functions declared in the file.
	 *
	 * @return ReflectionFunction[] An array of declared functions.
	 * @throws ReflectionException When the reflection fails, for instance when the file is not included.
	 */
	public function getFunctions(): array
	{
		return array_map(function (string $name): ReflectionFunction {
			return new ReflectionFunction($name);
		}, $this->reflector->functionNames);
	}

	/**
	 * Gets an array of declared constant names from the file.
	 *
	 * @return string[] An array of declared constant names.
	 */
	public function getConstantNames(): array
	{
		return $this->reflector->constNames;
	}

	/**
	 * Gets the filename of the file.
	 *
	 * @return string The filename.
	 */
	public function getFileName(): string
	{
		return $this->fileName;
	}

	/**
	 * Gets the pathname of the file.
	 *
	 * @return string
	 */
	public function getPathName(): string
	{
		return $this->pathName;
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
	 * Gets the string representation of this class.
	 *
	 * @return string The string representation (source) of the file.
	 */
	public function __toString(): string
	{
		return $this->source;
	}
}
