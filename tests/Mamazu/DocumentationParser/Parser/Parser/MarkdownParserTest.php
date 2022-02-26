<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\MarkdownParser;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class MarkdownParserTest extends TestCase
{
	private vfsStreamDirectory $workDir;

	private MarkdownParser $markdownParser;

	protected function setUp(): void
	{
		$this->workDir = vfsStream::setup('workDir');
		$this->markdownParser = new MarkdownParser();
	}

	public function testAParser(): void
	{
		$this->assertInstanceOf(ParserInterface::class, $this->markdownParser);
	}

	public function testCanOnlyParseMarkdown(): void
	{
		$this->assertTrue($this->markdownParser->canParse('docs.md'));
		$this->assertFalse($this->markdownParser->canParse('hello.py'));
		$this->assertTrue($this->markdownParser->canParse('hello.MD'));
	}

	public function testParsesAMarkdownFileWithoutCode(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('simple_file.md');
		$file->setContent('Hello<a href="something"></a>');
		$this->workDir->addChild($file);

		$this->assertCount(0, $this->markdownParser->parse('vfs://workDir/simple_file.md'));
	}

	public function testParsesAMarkdownFileWithACodeBlock(): void
	{
		$file = vfsStream::newFile('simple_code.md');
		$file->setContent(
			<<<MD
Hello<a href="something"></a>
```html
<img src="picture.html" alt="Some picture"/>
```
MD
		);
		$this->workDir->addChild($file);

		$result = $this->markdownParser->parse('vfs://workDir/simple_code.md');
		$this->assertCount(1, $result);
		$this->assertStringContainsString('simple_code.md', $result[0]->getFileName());
		$this->assertSame(2, $result[0]->getRelativeLineNumber());
		$this->assertSame('<img src="picture.html" alt="Some picture"/>', $result[0]->getContent());
		$this->assertSame('html', $result[0]->getType());
	}

	public function testParsesAMarkdownFileWithMultipleCodeBlocks(): void
	{
		$file = vfsStream::newFile('multiple_code.md');
		$file->setContent(
			<<<MD
Hello<a href="something"></a>
```html
<img src="picture.html" alt="Some picture"/>
```
<p>Hello </p>
```php
<?php
echo "Hello";
```
MD
		);
		$this->workDir->addChild($file);
		$result = $this->markdownParser->parse('vfs://workDir/multiple_code.md');
		$this->assertCount(2, $result);
		$this->assertStringContainsString('multiple_code.md', $result[0]->getFileName());
		$this->assertSame(2, $result[0]->getRelativeLineNumber());
		$this->assertSame('<img src="picture.html" alt="Some picture"/>', $result[0]->getContent());
		$this->assertSame('html', $result[0]->getType());
		$this->assertStringContainsString('multiple_code.md', $result[1]->getFileName());
		$this->assertSame(6, $result[1]->getRelativeLineNumber());
		$this->assertSame("<?php\necho \"Hello\";", $result[1]->getContent());
		$this->assertSame('php', $result[1]->getType());
	}
}
