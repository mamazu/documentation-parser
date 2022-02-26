<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorAggregator;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatorAggregatorTest extends TestCase
{
	private ValidatorAggregator $validatorAggregator;

	protected function setUp(): void
	{
		$this->validatorAggregator = new ValidatorAggregator([]);
	}

	public function testAParser(): void
	{
		$this->assertInstanceOf(ValidatorInterface::class, $this->validatorAggregator);
	}

	public function testCanAddAValidator(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);

		/** @var ValidatorInterface|MockObject $validator */
		$validator = $this->createMock(ValidatorInterface::class);
		$this->validatorAggregator->addValidator('php', $validator);

		$this->assertCount(0, $this->validatorAggregator->validate($block));
	}

	public function testDelegatesTheValidation(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getType' => 'php',
		]);

		/** @var ValidatorInterface|MockObject $validator1 */
		$validator1 = $this->createMock(ValidatorInterface::class);
		$validator1
			->expects($this->once())
			->method('validate')
			->with($block)
			->willReturn([new Error('hello.md', 0, 'Error')])
		;

		/** @var ValidatorInterface|MockObject $validator2 */
		$validator2 = $this->createMock(ValidatorInterface::class);
		$validator2->expects($this->never())->method('validate');

		$this->validatorAggregator = new ValidatorAggregator([
			'php' => $validator1,
			'python' => $validator2,
		]);

		$this->assertCount(1, $this->validatorAggregator->validate($block));
	}

	public function testReturnsNoErrorsIfTheValidatorIsNotDefined(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class, [

			'getType' => 'php',
		]);
		$this->assertSame([], $this->validatorAggregator->validate($block));
	}
}
