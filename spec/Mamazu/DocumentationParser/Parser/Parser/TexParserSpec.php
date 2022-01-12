<?php

namespace spec\Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

class TexParserSpec extends ObjectBehavior
{
	public vfsStreamDirectory $workDir;

	public function let(): void
	{
		$this->workDir = vfsStream::setup('workDir');
	}

	public function it_is_a_parser(): void
	{
		$this->shouldImplement(ParserInterface::class);
	}

	public function it_only_allows_rst_files(): void
	{
		$this->canParse('docs.tex')->shouldReturn(true);
		$this->canParse('hello.py')->shouldReturn(false);
		$this->canParse('hello.TEX')->shouldReturn(true);
	}

	public function it_parses_a_file_without_code(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('simple_file.tex');
		$file->setContent('\\begin{document}\\end{document}');
		$this->workDir->addChild($file);

		$this->parse('vfs://workDir/simple_file.tex')->shouldHaveCount(0);
	}

	public function it_skips_blocks_without_language(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('simple_file.tex');
		$file->setContent(<<<LATEX
        \\begin{document}
        \\begin{lstlisting}
        print("Hello World")
        \\end{lstlisting}
        \\end{document}
        LATEX);
		$this->workDir->addChild($file);

		$this->parse('vfs://workDir/simple_file.tex')->shouldHaveCount(0);
	}

	public function it_parses_a_file_with_python_code(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('multiple_code.tex');
		$file->setContent(<<<LATEX
        \\begin{document}
        \\begin{lstlisting}[language=Python]
        print("Hello World")
        \\end{lstlisting}
        \\end{document}
        LATEX);
		$this->workDir->addChild($file);

		$result = $this->parse('vfs://workDir/multiple_code.tex');
		$result->shouldHaveCount(1);
		$result[0]->getFileName()->shouldContain('multiple_code.tex');
		$result[0]->getRelativeLineNumber()->shouldBe(2);
		$result[0]->getContent()->shouldBe('print("Hello World")');
		$result[0]->getType()->shouldBe('python');
	}

	public function it_parses_a_single_line_code(): void
	{
		# Setting up the file
		$file = vfsStream::newFile('single_line_code.tex');
		$file->setContent(<<<LATEX
        \\begin{document}
        \\begin{lstlisting}[language=PHP] echo "Testing"; \\end{lstlisting}
        \\end{document}
        LATEX);
		$this->workDir->addChild($file);

		$result = $this->parse('vfs://workDir/single_line_code.tex');
		$result->shouldHaveCount(1);
		$result[0]->getFileName()->shouldContain('single_line_code.tex');
		$result[0]->getRelativeLineNumber()->shouldBe(2);
		$result[0]->getContent()->shouldBe('echo "Testing";');
		$result[0]->getType()->shouldBe('php');
	}
}
