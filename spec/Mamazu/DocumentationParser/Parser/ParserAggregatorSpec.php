<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Parser;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserAggregatorSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith([]);
    }

    public function it_is_a_parser(): void
    {
        $this->beAnInstanceOf(ParserInterface::class);
    }

    public function it_parses_everything(): void
    {
        $this->canParse('somefile')->shouldReturn(true);
    }

    public function it_can_add_a_parser(ParserInterface $parser): void
    {
        $this->addParser('php', $parser);
    }

    public function it_deligates_the_parsing(ParserInterface $parser1, ParserInterface $parser2)
    {
        $this->beConstructedWith(['md' => $parser1, 'rst' => $parser2]);

        $parser1->canParse('hello.md')->willReturn(true);
        $parser1->parse('hello.md')->willReturn([new Block('hello.md', '', 0, 'php')]);

        $parser2->canParse('hello.md')->willReturn(false);
        $parser2->parse(Argument::any())->shouldNotBeCalled();

        $this->parse('hello.md')->shouldHaveCount(1);
    }
}
