<?php declare(strict_types=1);
/**
 * This file is part of marijnvanwezel/reflection-file.
 *
 * (c) Marijn van Wezel <marijnvanwezel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ReflectionFile\Phpunit\Unit;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use ParseError;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionFile\ReflectionFile;
use SplFileInfo;

/**
 * @covers \ReflectionFile\ReflectionFile
 */
class ReflectionFileTest extends TestCase
{
	private vfsStreamFile $phpFile;

	/**
	 * @inheritDoc
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->phpFile = vfsStream::newFile('test.php')->at(vfsStream::setup());
		$this->fileInfo = new SplFileInfo($this->phpFile->url());
	}

	/**
	 * @doesNotPerformAssertions
	 * @throws ReflectionException
	 */
	public function testEmptyFile()
	{
		new ReflectionFile($this->fileInfo);
	}

	public function testUnreadableFile(): void
	{
		$this->phpFile->chmod(0);

		$this->expectException(ReflectionException::class);

		new ReflectionFile($this->fileInfo);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testGetFilename(): void
	{
		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($this->phpFile->getName(), $reflectionFile->getFileName());
	}

	public function testGetSource(): void
	{
		$this->phpFile->setContent('Testing is a virtue!');

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame('Testing is a virtue!', $reflectionFile->getSource());
	}

	/**
	 * @dataProvider provideGetClassNamesData
	 */
	public function testGetClassNames(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getClassNames());
	}

	/**
	 * @dataProvider provideGetTraitNamesData
	 */
	public function testGetTraitNames(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getTraitNames());
	}

	/**
	 * @dataProvider provideGetInterfaceNamesData
	 */
	public function testGetInterfaceNames(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getInterfaceNames());
	}

	/**
	 * @dataProvider provideGetEnumNamesData
	 */
	public function testGetEnumNames(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getEnumNames());
	}

	/**
	 * @dataProvider provideGetFunctionNamesData
	 */
	public function testGetFunctionNames(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getFunctionNames());
	}

	/**
	 * @dataProvider provideGetInterfaceNamesData
	 */
	public function getConstantNamesData(string $source, array $expected): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expected, $reflectionFile->getConstantNames());
	}

	public function testParseError(): void
	{
		$this->phpFile->setContent('<?php bogus_content');

		$this->expectException(ParseError::class);

		new ReflectionFile($this->fileInfo);
	}

	public function provideGetClassNamesData(): array
	{
		return [
			['', []],
			['class Foobar {}', []],
			['<?php class Foobar {}', ['Foobar']],
			['<?php namespace Foo; class Bar {}', ['Foo\Bar']],
			['<?php namespace { class Bar {} }', ['Bar']],
			['<?php namespace Foo { class Bar {} }', ['Foo\Bar']],
			['<?php namespace Foo { class Bar {} } namespace Bar { class Foo {} }', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo; class Bar {} namespace Bar; class Foo {}', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo { class Bar {} } namespace { class Bar {} }', ['Foo\Bar', 'Bar']],
			['<?php namespace { class Bar {} }', ['Bar']],
			['<?php namespace Bar { CLASS Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { trait Bar {} class Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { trait Foo {} class Bar {} }', ['Bar\Bar']],
			['<?php namespace { class Bar {} } namespace Bar { class Bar {} } namespace Foo { class Bar {} }', ['Bar', 'Bar\Bar', 'Foo\Bar']],
			['<?php namespace {}', []]
		];
	}

	public function provideGetTraitNamesData(): array
	{
		return [
			['', []],
			['trait Foobar {}', []],
			['<?php trait Foobar {}', ['Foobar']],
			['<?php namespace Foo; trait Bar {}', ['Foo\Bar']],
			['<?php namespace { trait Bar {} }', ['Bar']],
			['<?php namespace Foo { trait Bar {} }', ['Foo\Bar']],
			['<?php namespace Foo { trait Bar {} } namespace Bar { trait Foo {} }', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo; trait Bar {} namespace Bar; trait Foo {}', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo { trait Bar {} } namespace { trait Bar {} }', ['Foo\Bar', 'Bar']],
			['<?php namespace { trait Bar {} }', ['Bar']],
			['<?php namespace Bar { TRAIT Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { trait Bar {} class Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { trait Bar {} class Foo {} }', ['Bar\Bar']],
			['<?php namespace { trait Bar {} } namespace Bar { trait Bar {} } namespace Foo { trait Bar {} }', ['Bar', 'Bar\Bar', 'Foo\Bar']],
			['<?php namespace {}', []]
		];
	}

	public function provideGetInterfaceNamesData(): array
	{
		return [
			['', []],
			['interface Foobar {}', []],
			['<?php interface Foobar {}', ['Foobar']],
			['<?php namespace Foo; interface Bar {}', ['Foo\Bar']],
			['<?php namespace { interface Bar {} }', ['Bar']],
			['<?php namespace Foo { interface Bar {} }', ['Foo\Bar']],
			['<?php namespace Foo { interface Bar {} } namespace Bar { interface Foo {} }', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo; interface Bar {} namespace Bar; interface Foo {}', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo { interface Bar {} } namespace { interface Bar {} }', ['Foo\Bar', 'Bar']],
			['<?php namespace { interface Bar {} }', ['Bar']],
			['<?php namespace Bar { INTERFACE Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { interface Bar {} class Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { interface Bar {} class Foo {} }', ['Bar\Bar']],
			['<?php namespace { interface Bar {} } namespace Bar { interface Bar {} } namespace Foo { interface Bar {} }', ['Bar', 'Bar\Bar', 'Foo\Bar']],
			['<?php namespace {}', []]
		];
	}

	public function provideGetEnumNamesData(): array
	{
		return [
			['', []],
			['enum Foobar {}', []],
			['<?php enum Foobar {}', ['Foobar']],
			['<?php namespace Foo; enum Bar {}', ['Foo\Bar']],
			['<?php namespace { enum Bar {} }', ['Bar']],
			['<?php namespace Foo { enum Bar {} }', ['Foo\Bar']],
			['<?php namespace Foo { enum Bar {} } namespace Bar { enum Foo {} }', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo; enum Bar {} namespace Bar; enum Foo {}', ['Foo\Bar', 'Bar\Foo']],
			['<?php namespace Foo { enum Bar {} } namespace { enum Bar {} }', ['Foo\Bar', 'Bar']],
			['<?php namespace { enum Bar {} }', ['Bar']],
			['<?php namespace Bar { ENUM Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { enum Bar {} class Bar {} }', ['Bar\Bar']],
			['<?php namespace Bar { enum Bar {} class Foo {} }', ['Bar\Bar']],
			['<?php namespace { enum Bar {} } namespace Bar { enum Bar {} } namespace Foo { enum Bar {} }', ['Bar', 'Bar\Bar', 'Foo\Bar']],
			['<?php namespace {}', []]
		];
	}

	public function provideGetFunctionNamesData(): array
	{
		return [
			['', []],
			['function foobar {}', []],
			['<?php function foobar() {}', ['foobar']],
			['<?php function foobar   () {}', ['foobar']],
			['<?php namespace Foo; function bar() {}', ['Foo\bar']],
			['<?php namespace { function bar() {} }', ['bar']],
			['<?php namespace Foo { function bar() {} }', ['Foo\bar']],
			['<?php namespace Foo { function bar() {} } namespace Bar { function foo() {} }', ['Foo\bar', 'Bar\foo']],
			['<?php namespace Foo; function bar() {} namespace Bar; function foo() {}', ['Foo\bar', 'Bar\foo']],
			['<?php namespace Foo { function bar() {} } namespace { function bar() {} }', ['Foo\bar', 'bar']],
			['<?php namespace { function bar() {} }', ['bar']],
			['<?php namespace Bar { FUNCTION bar() {} }', ['Bar\bar']],
			['<?php namespace Bar { function bar() {} class bar {} }', ['Bar\bar']],
			['<?php namespace Bar { function bar() {} class Foo {} }', ['Bar\bar']],
			['<?php namespace { function bar() {} } namespace Bar { function bar() {} } namespace Foo { function bar() {} }', ['bar', 'Bar\bar', 'Foo\bar']],
			['<?php namespace {}', []]
		];
	}

	public function provideGetConstantNamesData(): array
	{
		return [
			['', []],
			['const foobar = "Hello"', []],
			['<?php const foobar = "hello";', ['foobar']],
			['<?php namespace Foo; const bar = "hello";', ['Foo\bar']],
			['<?php namespace { const bar = "hello"; }', ['bar']],
			['<?php namespace Foo { const bar = "hello"; }', ['Foo\bar']],
			['<?php namespace Foo { const bar = "hello"; } namespace Bar { const foo = "hello"; }', ['Foo\bar', 'Bar\foo']],
			['<?php namespace Foo; const bar = "hello"; namespace Bar; const foo = "hello";', ['Foo\bar', 'Bar\foo']],
			['<?php namespace Foo { const bar = "hello"; } namespace { const bar = "hello"; }', ['Foo\bar', 'bar']],
			['<?php namespace { const bar = "hello"; }', ['bar']],
			['<?php namespace Bar { CONST bar = "hello"; }', ['Bar\bar']],
			['<?php namespace Bar { const bar = "hello"; class bar {} }', ['Bar\bar']],
			['<?php namespace Bar { const bar = "hello"; class Foo {} }', ['Bar\bar']],
			['<?php namespace { const bar = "hello"; } namespace Bar { const bar = "hello"; } namespace Foo { const bar = "hello"; }', ['bar', 'Bar\bar', 'Foo\bar']],
			['<?php namespace {}', []]
		];
	}
}
