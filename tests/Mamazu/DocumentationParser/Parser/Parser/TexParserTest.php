<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Parser\Parser\TexParser;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class TexParserTest extends TestCase
{
	public vfsStreamDirectory $workDir;

	private TexParser $texParser;

	protected function setUp(): void
	{
		$this->workDir = vfsStream::setup('workDir');
		$this->texParser = new TexParser();
	}

	public function testAParser(): void
	{
		$this->assertInstanceOf(ParserInterface::class, $this->texParser);
	}

	public function testOnlyAllowsRstFiles(): void
	{
		$this->assertTrue($this->texParser->canParse('docs.tex'));
		$this->assertFalse($this->texParser->canParse('hello.py'));
		$this->assertTrue($this->texParser->canParse('hello.TEX'));
	}

	public function testParsesAFileWithoutCode(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('simple_file.tex');
		$file->setContent('\begin{document}\end{document}');
		$this->workDir->addChild($file);
		$this->assertCount(0, $this->texParser->parse('vfs://workDir/simple_file.tex'));
	}

	public function testSkipsBlocksWithoutLanguage(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('simple_file.tex');
		$file->setContent(
			<<<LATEX
\\begin{document}
\\begin{lstlisting}
print("Hello World")
\\end{lstlisting}
\\end{document}
LATEX
		);
		$this->workDir->addChild($file);
		$this->assertCount(0, $this->texParser->parse('vfs://workDir/simple_file.tex'));
	}

	public function testParsesAFileWithPythonCode(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('multiple_code.tex');
		$file->setContent(
			<<<LATEX
\\begin{document}
\\begin{lstlisting}[language=Python]
print("Hello World")
\\end{lstlisting}
\\end{document}
LATEX
		);
		$this->workDir->addChild($file);
		$result = $this->texParser->parse('vfs://workDir/multiple_code.tex');
		$this->assertCount(1, $result);
		$this->assertStringContainsString('multiple_code.tex', $result[0]->getFileName());
		$this->assertSame(2, $result[0]->getRelativeLineNumber());
		$this->assertSame('print("Hello World")', $result[0]->getContent());
		$this->assertSame('python', $result[0]->getType());
	}

	public function testParsesASingleLineCode(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('single_line_code.tex');
		$file->setContent(
			<<<LATEX
\\begin{document}
\\begin{lstlisting}[language=PHP] echo "Testing"; \\end{lstlisting}
\\end{document}
LATEX
		);
		$this->workDir->addChild($file);
		$result = $this->texParser->parse('vfs://workDir/single_line_code.tex');
		$this->assertCount(1, $result);
		$this->assertStringContainsString('single_line_code.tex', $result[0]->getFileName());
		$this->assertSame(2, $result[0]->getRelativeLineNumber());
		$this->assertSame('echo "Testing";', $result[0]->getContent());
		$this->assertSame('php', $result[0]->getType());
	}
}
