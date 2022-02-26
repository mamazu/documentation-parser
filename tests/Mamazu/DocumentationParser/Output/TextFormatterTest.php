<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Output\TextFormatter;
use PHPUnit\Framework\TestCase;

class TextFormatterTest extends TestCase
{
	private TextFormatter $textFormatter;

	protected function setUp(): void
	{
		parent::setUp();
		$this->textFormatter = new TextFormatter();
	}

	public function testFormatsTheAnError(): void
	{
		$errors = [new Error('some_file.php', 3, 'Unknown thing')];
		$this->assertSame('some_file.php:3 ---- Unknown thing', $this->textFormatter->format($errors));
	}

	public function testFormatsTheErrorArray(): void
	{
		$errors = [new Error('some_file.php', 3, 'Unknown thing'), new Error('some_file.php', 10, 'Error in format')];
		$this->assertSame("some_file.php:3 ---- Unknown thing\nsome_file.php:10 ---- Error in format", $this->textFormatter->format($errors));
	}
}
