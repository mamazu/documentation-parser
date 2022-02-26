<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Validator\Yaml;

use InvalidArgumentException;
use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use Mamazu\DocumentationParser\Validator\Yaml\YamlValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlValidatorTest extends TestCase
{
	private YamlValidator $yamlValidator;

	/**
	 * @var MockObject|Parser
	 */
	private $parser;

	protected function setUp(): void
	{
		$this->parser = $this->createMock(Parser::class);
		$this->yamlValidator = new YamlValidator($this->parser);
	}

	public function testAValidator(): void
	{
		$this->assertInstanceOf(ValidatorInterface::class, $this->yamlValidator);
	}

	public function testValidatesABlock(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [

			'getContent' => 'test: true',
		]);
		$this->parser->expects($this->atLeastOnce())->method('parse')->with($this->equalTo('test: true'));
		$this->assertSame([], $this->yamlValidator->validate($block));
	}

	public function testValidatesABlockAndAddsErrorsIfParsingFails(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => 'test: true',
			'getRelativeLineNumber' => 10,
			'getType' => 'yaml',
			'getFileName' => 'test.md',
		]);
		$parseException = new ParseException('Invalid YAML', 10);
		$this->parser
			->expects($this->atLeastOnce())
			->method('parse')
			->with($this->equalTo('test: true'))
			->willThrowException($parseException)
	   ;
		$result = $this->yamlValidator->validate($block);
		$this->assertCount(1, $result);
		$this->assertEquals($result[0], new Error('test.md', 20, 'Invalid YAML at line 10', 'yaml'));
	}

	public function testAddsAnErrorIfTheWholeDocumentIsInvalid(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => 'test: true',
			'getRelativeLineNumber' => 10,
			'getType' => 'yaml',
			'getFileName' => 'test.md',
		]);
		$parseException = new ParseException('Invalid YAML', -1);
		$this->parser
			->expects($this->atLeastOnce())
			->method('parse')
			->with($this->equalTo('test: true'))
			->willThrowException($parseException)
   ;
		$result = $this->yamlValidator->validate($block);
		$this->assertCount(1, $result);
		$this->assertEquals($result[0], new Error('test.md', 10, 'Invalid YAML', 'yaml'));
	}

	public function testValidatesIndentedBlock(): void
	{
		$yamlContent = <<<YAML
	fixtures:
		testing: 1234
YAML;

		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getContent' => $yamlContent,
		]);

		$this->parser->parse(
			<<<YAML
fixtures:
	testing: 1234
YAML
		);

		$this->assertCount(0, $this->yamlValidator->validate($block));
	}

	public function testValidatesAStrangelyIndentedBlock(): void
	{
		/** @var Block|MockObject $block */
		$block = $this->createConfiguredMock(Block::class, [
			'getFileName' => 'readme.md',
			'getRelativeLineNumber' => 10,
			'getContent' => <<<YAML
\tfixtures:
testing: 1234
YAML
		]);

		$this->expectExceptionObject(new InvalidArgumentException('Expected indentation "\t" in file readme.md on line 11'));
		$this->yamlValidator->validate($block);
	}
}
