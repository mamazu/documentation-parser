<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Parser\Parser;

use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class MarkdownParserSpec extends ObjectBehavior
{
    /** @var vfsStreamDirectory */
    private $workDir;

    public function let(): void
    {
        $this->workDir = vfsStream::setup('workDir');
    }

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
        # Setting up the file
        $file = vfsStream::newFile('simple_file.md');
        $file->setContent('Hello<a href="something"></a>');
        $this->workDir->addChild($file);

        $this->parse('vfs://workDir/simple_file.md')->shouldHaveCount(0);
    }

    public function it_parses_a_markdown_file_with_a_code_block(): void
    {
        $file = vfsStream::newFile('simple_code.md');
        $file->setContent(<<<MD
Hello<a href="something"></a>
```html
<img src="picture.html" alt="Some picture"/>
```
MD
);
        $this->workDir->addChild($file);

        $result = $this->parse('vfs://workDir/simple_code.md');

        $result->shouldHaveCount(1);
        $result[0]->getFileName()->shouldContain('simple_code.md');
        $result[0]->getRelativeLineNumber()->shouldBe(2);
        $result[0]->getContent()->shouldBe('<img src="picture.html" alt="Some picture"/>');
        $result[0]->getType()->shouldBe('html');
    }

    public function it_parses_a_markdown_file_with_multiple_code_blocks(): void
    {
        $file = vfsStream::newFile('multiple_code.md');
        $file->setContent(<<<MD
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

        $result = $this->parse('vfs://workDir/multiple_code.md');
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
