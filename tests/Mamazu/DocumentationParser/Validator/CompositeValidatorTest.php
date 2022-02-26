<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\CompositeValidator;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CompositeValidatorTest extends TestCase
{
	public function testAValidator(): void
	{
		$compositeValidator = new CompositeValidator([], true);
		$this->assertInstanceOf(ValidatorInterface::class, $compositeValidator);
	}

	public function testAddsAValidator(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);

		/** @var ValidatorInterface|MockObject $validator */
		$validator = $this->createMock(ValidatorInterface::class);
		$validator->expects($this->once())->method('validate')->willReturn([]);

		$compositeValidator = new CompositeValidator([]);
		$compositeValidator->addValidator($validator);

		$this->assertCount(0, $compositeValidator->validate($block));
	}

	public function testValidatesWithMultipleValidators(): void
	{
		/** @var ValidatorInterface|MockObject $validator1 */
		$validator1 = $this->createMock(ValidatorInterface::class);
		/** @var ValidatorInterface|MockObject $validator2 */
		$validator2 = $this->createMock(ValidatorInterface::class);
		/** @var ValidatorInterface|MockObject $validator3 */
		$validator3 = $this->createMock(ValidatorInterface::class);
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$validator1->expects($this->once())->method('validate')->with($block)->willReturn([new Error('', 1, '')]);
		$validator2->expects($this->once())->method('validate')->with($block)->willReturn([new Error('abc', 2, '')]);
		$validator3->expects($this->once())->method('validate')->with($block)->willReturn([]);

		$compositeValidator = new CompositeValidator([$validator1, $validator2, $validator3], true);
		$result = $compositeValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(2, $result);
	}

	public function testValidatesOnlyFirstValidatorIfItShouldStopOnError(): void
	{
		/** @var ValidatorInterface|MockObject $validator1 */
		$validator1 = $this->createMock(ValidatorInterface::class);
		/** @var ValidatorInterface|MockObject $validator2 */
		$validator2 = $this->createMock(ValidatorInterface::class);
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$validator1->expects($this->once())->method('validate')->with($block)->willReturn([new Error('', 1, '')]);
		$validator2->expects($this->never())->method('validate')->with($block);

		$compositeValidator = new CompositeValidator([$validator1, $validator2], false);
		$result = $compositeValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(1, $result);
	}
}
