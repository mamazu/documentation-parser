<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Output\JsonFormatter;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
	private JsonFormatter $jsonFormatter;

	protected function setUp(): void
	{
		$this->jsonFormatter = new JsonFormatter();
	}

	public function testReturnsNothingIfThereIsNoError(): void
	{
		$this->assertSame('', $this->jsonFormatter->format([]));
	}

	public function testFormatsTheAnError(): void
	{
		$errors = [new Error('some_file.php', 3, 'Unknown thing', 'yaml')];
		$this->assertSame(<<<JSON
[
    {
        "fileName": "some_file.php",
        "lineNumber": 3,
        "type": "yaml",
        "message": "Unknown thing"
    }
]
JSON
, $this->jsonFormatter->format($errors));
	}

	public function testFormatsTheErrorArray(): void
	{
		$errors = [new Error('some_file.php', 3, 'Unknown thing'), new Error('some_file.php', 10, 'Error in format')];
		$this->assertSame(<<<JSON
[
    {
        "fileName": "some_file.php",
        "lineNumber": 3,
        "type": null,
        "message": "Unknown thing"
    },
    {
        "fileName": "some_file.php",
        "lineNumber": 10,
        "type": null,
        "message": "Error in format"
    }
]
JSON
, $this->jsonFormatter->format($errors));
	}
}
