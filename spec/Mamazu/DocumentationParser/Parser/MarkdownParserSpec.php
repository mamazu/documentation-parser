<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Parser;

use Mamazu\DocumentationParser\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;

class MarkdownParserSpec extends ObjectBehavior
{
    public function it_is_a_parser(): void
    {
        $this->shouldImplement(ParserInterface::class);
    }

    public function it_can_only_parse_markdown(): void
    {
        $this->canParse('docs.md')->shouldReturn(true);
        $this->canParse('hello.py')->shouldReturn(false);
    }

    public function it_parses_a_markdown_file_without_code(): void
    {
        $this->parse(__DIR__ . '/../../../data/Markdown/simple_file.md')->shouldHaveCount(0);
    }

    public function it_parses_a_markdown_file_with_a_code_block(): void
    {
        $result = $this->parse(__DIR__ . '/../../../data/Markdown/simple_code.md');
        $result->shouldHaveCount(1);
        $result[0]->getFileName()->shouldContain('simple_code.md');
        $result[0]->getRelativeLineNumber()->shouldBe(2);
        $result[0]->getContent()->shouldBe('<img src="picture.html" alt="Some picture"/>');
        $result[0]->getType()->shouldBe('html');
    }

    public function it_parses_a_markdown_file_with_multiple_code_blocks(): void
    {
        $result = $this->parse(__DIR__ . '/../../../data/Markdown/multiple_code.md');
        $result->shouldHaveCount(2);

        $result[0]->getFileName()->shouldContain('multiple_code.md');
        $result[0]->getRelativeLineNumber()->shouldBe(2);
        $result[0]->getContent()->shouldBe('<img src="picture.html" alt="Some picture"/>');
        $result[0]->getType()->shouldBe('html');
        
        $result[1]->getFileName()->shouldContain('multiple_code.md');
        $result[1]->getRelativeLineNumber()->shouldBe(6);
        $result[1]->getContent()->shouldBe("<?php\necho \"Hello\";");
        $result[1]->getType()->shouldBe('php');
    }
}
