<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Output\VerboseFormatter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class VerboseFormatterTest extends TestCase
{
	private const FILE_NAME = 'vfs://workDir/error.php';

	private VerboseFormatter $verboseFormatter;

	private vfsStreamDirectory $workDir;

	protected function setUp(): void
	{
		parent::setUp();
		$this->workDir = vfsStream::setup('workDir');
		$this->verboseFormatter = new VerboseFormatter();

		$file = vfsStream::newFile('error.php');
		$file->setContent(<<<PHP
<?php
echo "Hello"
\$a = 10;

PHP);
		$this->workDir->addChild($file);
	}

	public function testFormatsTheAnError(): void
	{
		$errors = [
			new Error(self::FILE_NAME, 3, 'Expected ; got variable'),
		];

		$expected = <<<TXT

========== [@vfs://workDir/error.php:3] ==========
echo "Hello"
\$a = 10;
^^^ \e[31mExpected ; got variable\e[0m ^^^

==================================================
TXT;

		$this->assertSame(
			$expected,
			$this->verboseFormatter->format($errors)
		);
	}

	public function testFormatsTheMultipleErrors(): void
	{
		$errors = [
			new Error(self::FILE_NAME, 3, 'Expected ; got variable'),
			new Error(self::FILE_NAME, 4, 'Unexpected end of file'),
		];

		$expected = <<<TXT

========== [@vfs://workDir/error.php:3] ==========
echo "Hello"
\$a = 10;
^^^ \e[31mExpected ; got variable\e[0m ^^^

==================================================

========== [@vfs://workDir/error.php:4] ==========
\$a = 10;

^^^ \e[31mUnexpected end of file\e[0m ^^^
\$EOF\$
==================================================
TXT;

		$this->assertSame(
			$expected,
			$this->verboseFormatter->format($errors)
		);
	}
}
