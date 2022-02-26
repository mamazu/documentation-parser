<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Php\EnvValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnvValidatorTest extends TestCase
{
	private EnvValidator $envValidator;

	protected function setUp(): void
	{
		$this->envValidator = new EnvValidator();
	}

	public function testValidatesAValidFile()
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('APP_ENV=test');
		$block->expects($this->atLeastOnce())->method('getFileName')->willReturn('.env');
		$errors = $this->envValidator->validate($block);
		$this->assertCount(0, $errors);
	}

	public function testValidatesAnInvalidBlock()
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('APP_ENV="${something');
		$block->expects($this->atLeastOnce())->method('getFileName')->willReturn('.env');
		$block->expects($this->atLeastOnce())->method('getRelativeLineNumber')->willReturn(0);
		$block->expects($this->atLeastOnce())->method('getType')->willReturn('env');
		$errors = $this->envValidator->validate($block);
		$this->assertCount(1, $errors);
		$this->assertEquals(
			(new Error('.env', 1, 'Missing quote to end the value in ".env" at line 1.', 'env')),
			$errors[0]
		);
	}
}
