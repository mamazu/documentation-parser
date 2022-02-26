<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Php\PhpClassExistsValidator;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpParser\Error;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Parser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhpClassExistsValidatorTest extends TestCase
{
	private PhpClassExistsValidator $phpClassExistsValidator;

	/**
	 * @var Parser|MockObject
	 */
	private $parser;

	protected function setUp(): void
	{
		$this->parser = $this->createMock(Parser::class);
		$this->phpClassExistsValidator = new PhpClassExistsValidator(static function () {
			return true;
		}, $this->parser);
	}

	public function testAValidator(): void
	{
		$this->assertInstanceOf(ValidatorInterface::class, $this->phpClassExistsValidator);
	}

	public function testDoesNothingIfItIsAPhpTag(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => '<?php',
		]);
		$this->parser->expects($this->atLeastOnce())->method('parse')->with($this->equalTo('<?php'))->willReturn(null);
		$this->assertSame([], $this->phpClassExistsValidator->validate($block));
	}

	public function testDoesNotThrowAnExceptionIfTheParserReturnsNothing(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => '',
		]);
		$this->parser->expects($this->atLeastOnce())->method('parse')->with($this->equalTo('<?php '))->willReturn(null);
		$this->assertSame([], $this->phpClassExistsValidator->validate($block));
	}

	public function testValidatesSyntaxErrors(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => 'abc',
			'getType' => 'php',
			'getFileName' => 'some_file.php',
			'getRelativeLineNumber' => 10,
		]);
		$error = new Error('Syntax Error on line 10');

		$this->parser
			->expects($this->atLeastOnce())
			->method('parse')
			->with($this->equalTo('<?php abc'))
			->willThrowException($error);
		$result = $this->phpClassExistsValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(1, $result);
		$this->assertInstanceOf(\Mamazu\DocumentationParser\Error\Error::class, $result[0]);
	}

	public function testReturnsNoErrorsIfTheClassWasFound(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getType' => 'php',
			'getFileName' => 'some_file.php',
			'getContent' => 'use SomeClass',
			'getRelativeLineNumber' => 10,
		]);
		/** @var Use_|MockObject $useStatement */
		$useStatement = $this->createMock(Use_::class);
		/** @var UseUse|MockObject $useObject */
		$useObject = $this->createMock(UseUse::class);
		$this->phpClassExistsValidator = new PhpClassExistsValidator(static function (string $className) {
			return $className === 'SomeClass';
		}, $this->parser);
		$this->parser->expects($this->atLeastOnce())->method('parse')->with($this->equalTo('<?php use SomeClass'))->willReturn([$useStatement]);
		$useStatement->uses = [$useObject];
		$useObject->name = 'SomeClass';
		$useObject->method('getLine')->willReturn(1);
		$array = $this->phpClassExistsValidator->validate($block);
		$this->assertIsIterable($array);
		$this->assertCount(0, $array);
	}

	public function testReturnsAnErrorIfClassNameWasNotFound(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getType' => 'php',
			'getFileName' => 'some_file.php',
			'getContent' => 'use SomeClass',
			'getRelativeLineNumber' => 10,
		]);
		/** @var Use_|MockObject $useStatement */
		$useStatement = $this->createMock(Use_::class);
		/** @var UseUse|MockObject $useObject */
		$useObject = $this->createMock(UseUse::class);
		$this->phpClassExistsValidator = new PhpClassExistsValidator(static function (string $className) {
			return $className !== 'SomeClass';
		}, $this->parser);
		$this->parser->method('parse')->with('<?php use SomeClass')->willReturn([$useStatement]);
		$useStatement->uses = [$useObject];
		$useObject->name = 'SomeClass';
		$useObject->method('getLine')->willReturn(1);
		$array = $this->phpClassExistsValidator->validate($block);
		$this->assertIsIterable($array);
		$this->assertCount(1, $array);
	}
}
