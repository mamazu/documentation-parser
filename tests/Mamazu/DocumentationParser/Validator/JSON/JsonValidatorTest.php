<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\JSON;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\JSON\JsonValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonValidatorTest extends TestCase
{
	private JsonValidator $jsonValidator;

	protected function setUp(): void
	{
		$this->jsonValidator = new JsonValidator();
	}

	public function testValidatesAValidBlock(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => '{}',
		]);

		$result = $this->jsonValidator->validate($block);
		$this->assertCount(0, $result);
	}

	public function testValidatesAnInvalidBlock(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => '{abc}',
			'getFileName' => 'test.json',
			'getRelativeLineNumber' => 0,
			'getType' => 'json',
		]);
		$result = $this->jsonValidator->validate($block);
		$this->assertCount(1, $result);
		$this->assertEquals(
			new Error('test.json', 1, 'Syntax error', 'json'),
			$result[0]
		);
	}
}
