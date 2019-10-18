<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use PhpSpec\ObjectBehavior;

class ErrorSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('some_filename.php', 10, 'Error occoured');
    }

    public function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Error occoured');
    }

    public function it_has_a_line_number(): void
    {
        $this->getLineNumber()->shouldReturn(10);
    }

    public function it_has_a_file_name(): void
    {
        $this->getFileName()->shouldReturn('some_filename.php');
    }

    public function it_can_be_created_from_block(Block $block): void
    {
        $block->getFileName()->willReturn('abc.de');
        $block->getRelativeLineNumber()->willReturn(10);
        $block->getType()->willReturn('php');

        $this->beConstructedThrough('errorFromBlock', [$block, 10, 'Some message']);

        $this->getFileName()->shouldReturn('abc.de');
        $this->getMessage()->shouldReturn('[php] Some message');
        $this->getLineNumber()->shouldReturn(19);
    }

}
