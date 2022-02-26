<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\XML;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use Mamazu\DocumentationParser\Validator\XML\XMLValidValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XMLValidValidatorTest extends TestCase
{
	private XMLValidValidator $xMLValidValidator;

	protected function setUp(): void
	{
		$this->xMLValidValidator = new XMLValidValidator();
	}

	public function testAValidator(): void
	{
		$this->assertInstanceOf(ValidatorInterface::class, $this->xMLValidValidator);
	}

	public function testProducesNoErrorsIfTheXmlIsValid(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('
<Hello>
    This is text
</Hello>
');
		$result = $this->xMLValidValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(0, $result);
	}

	public function testReturnsErrorsIfTheXmlIsInvalid(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('
<Hello =>
    This is text
</Hello>
');
		$block->expects($this->atLeastOnce())->method('getType')->willReturn('xml');
		$block->expects($this->atLeastOnce())->method('getFileName')->willReturn('abc.xml');
		$block->expects($this->atLeastOnce())->method('getRelativeLineNumber')->willReturn(10);
		$result = $this->xMLValidValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(4, $result);
	}

	public function testValidatesConsecutiveBlocksCorrectly(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		/** @var Block|MockObject $block2 */
		$block2 = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('
<Hello =>
    This is text
</Hello>
');
		$block->expects($this->atLeastOnce())->method('getType')->willReturn('xml');
		$block->expects($this->atLeastOnce())->method('getFileName')->willReturn('abc.xml');
		$block->expects($this->atLeastOnce())->method('getRelativeLineNumber')->willReturn(10);
		$result = $this->xMLValidValidator->validate($block);
		$this->assertIsIterable($result);
		$this->assertCount(4, $result);
		$block2->expects($this->atLeastOnce())->method('getContent')->willReturn('
<Hello>
    This is text
</Hello>
');
		$this->assertCount(0, $this->xMLValidValidator->validate($block2));
	}
}
