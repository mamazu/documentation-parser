<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Utils;

use Mamazu\DocumentationParser\Utils\PhpCodeEnsurer;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class PhpCodeEnsurerTest extends TestCase
{
	private PhpCodeEnsurer $phpCodeEnsurer;

	/**
	 * @var MockObject|Filesystem
	 */
	private $filesystem;

	protected function setUp(): void
	{
		$this->filesystem = $this->createMock(Filesystem::class);
		$this->phpCodeEnsurer = new PhpCodeEnsurer($this->filesystem);
	}

	public function testACodeEnsurer(): void
	{
		$this->assertInstanceOf(PhpCodeEnsurerInterface::class, $this->phpCodeEnsurer);
	}

	public function testPrefixesPhpCode(): void
	{
		$this->assertSame('<?php echo "Hello";', $this->phpCodeEnsurer->getPHPCode('echo "Hello";'));
	}

	public function testLeavesPhpCodeUntouched(): void
	{
		$this->assertSame('<?php print_r(["Hello"]);', $this->phpCodeEnsurer->getPHPCode(' <?php print_r(["Hello"]);'));
	}

	public function testPutsThePhpCodeInAFile(): void
	{
		$this->filesystem
			->expects($this->once())
			->method('dumpFile')
			->with($this->equalTo('/tmp/hello.php'))
   ;

		$this->phpCodeEnsurer->putPhpCodeToFile('<?php', '/tmp/hello.php');
	}

	public function testPrefixesMemeberFunctionsWithClasses()
	{
		$this->assertSame('<?php namespace Mamazu\DocumentationParser;
class AnonymousClassThatWeNeedForItToBeValidPhp {
	public function sayHello() {}
}', $this->phpCodeEnsurer->getPHPCode('public function sayHello() {}'));
	}

	public function testPrefixesMemeberFunctionsWithClassesWithComment()
	{
		$this->assertSame('<?php namespace Mamazu\DocumentationParser;
class AnonymousClassThatWeNeedForItToBeValidPhp {
	/** */ public function testingComment() {}
}', $this->phpCodeEnsurer->getPHPCode('/** */ public function testingComment() {}'));
	}

	public function testPrefixesMemeberFunctionsWithClassesWithCommentAndPhpTag()
	{
		$this->assertSame('<?php namespace Mamazu\DocumentationParser;
class AnonymousClassThatWeNeedForItToBeValidPhp {
	 /** */ public function sayTestingHello() {}
}', $this->phpCodeEnsurer->getPHPCode('<?php /** */ public function sayTestingHello() {}'));
	}

	public function testDoesNotPrefixClasses()
	{
		$this->assertSame('<?php class Hello { public function sayHello() {}}', $this->phpCodeEnsurer->getPHPCode('class Hello { public function sayHello() {}}'));
	}

	public function testAllowsEmptyClasses()
	{
		$this->assertSame(
			'<?php class Hello {}',
			$this->phpCodeEnsurer->getPHPCode('<?php class Hello {}')
		);
	}

	public function testDoesNotPrefixClassesWithPhpTags()
	{
		$this->assertSame('<?php class Hello { public function sayHello() {}}', $this->phpCodeEnsurer->getPHPCode('<?php class Hello { public function sayHello() {}}'));
	}

	public function testThrowsAnErrorIfTheDirectoryCanNotBeCreated(): void
	{
		$this->filesystem
			->expects($this->atLeastOnce())
			->method('dumpFile')
			->with($this
			->equalTo('hello.php'))
			->willThrowException(new IOException('Could not create directory'))
		;
		$this->expectException(IOException::class);
		$this->phpCodeEnsurer->putPhpCodeToFile('<?php', 'hello.php');
	}
}
