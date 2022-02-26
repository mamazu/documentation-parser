<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\Bash;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Bash\BashValidator;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BashValidatorTest extends TestCase
{
	private BashValidator $bashValidator;

	protected function setUp(): void
	{
		$this->bashValidator = new BashValidator('bash');
	}

	public function testAValidator(): void
	{
		$this->assertInstanceOf(ValidatorInterface::class, $this->bashValidator);
	}

	public function testValidatesABlockWithNoErrors(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('echo "Hello World"');
		$this->assertSame([], $this->bashValidator->validate($block));
	}

	public function testValidatesABlockWithErrors(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createMock(Block::class);
		$block->expects($this->atLeastOnce())->method('getContent')->willReturn('echo "Hello World');
		$block->expects($this->atLeastOnce())->method('getFileName')->willReturn('test.sh');
		$block->expects($this->atLeastOnce())->method('getRelativeLineNumber')->willReturn(10);
		$block->expects($this->atLeastOnce())->method('getType')->willReturn('bash');

		$this->assertEquals(
			[
				new Error('test.sh', 11, 'unexpected EOF while looking for matching `"\'', 'bash'),
				new Error('test.sh', 12, 'syntax error: unexpected end of file', 'bash'),
			],
			$this->bashValidator->validate($block)
		);
	}
}
