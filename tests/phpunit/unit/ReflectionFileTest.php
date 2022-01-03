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
	 * @dataProvider provideGetDeclaredClassnamesData
	 */
	public function testGetDeclaredClassnames(string $source, array $expectedClassNames): void
	{
		$this->phpFile->setContent($source);

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->assertSame($expectedClassNames, $reflectionFile->getDeclaredClassnames());
	}

	public function testParseError(): void
	{
		$this->phpFile->setContent('<?php bogus_content');

		$reflectionFile = new ReflectionFile($this->fileInfo);

		$this->expectException(ParseError::class);

		$reflectionFile->getDeclaredClassnames();
	}

	public function provideGetDeclaredClassnamesData(): array
	{
		return [
			['', []],
			['class Foobar {}', []],
			['<?php class Foobar {}', ['\Foobar']],
			['<?php namespace Foo; class Bar {}', ['\Foo\Bar']],
			['<?php namespace { class Bar {} }', ['\Bar']],
			['<?php namespace Foo { class Bar {} }', ['\Foo\Bar']],
			['<?php namespace Foo { class Bar {} } namespace Bar { class Foo {} }', ['\Foo\Bar', '\Bar\Foo']],
			['<?php namespace Foo; class Bar {} namespace Bar; class Foo {}', ['\Foo\Bar', '\Bar\Foo']],
			['<?php namespace Foo { class Bar {} } namespace { class Bar {} }', ['\Foo\Bar', '\Bar']],
			['<?php namespace { class Bar {} }', ['\Bar']],
			['<?php namespace Bar { class Bar {} }', ['\Bar\Bar']],
			['<?php namespace { class Bar {} } namespace Bar { class Bar {} } namespace Foo { class Bar {} }', ['\Bar', '\Bar\Bar', '\Foo\Bar']],
			['<?php namespace {}', []]
		];
	}
}
