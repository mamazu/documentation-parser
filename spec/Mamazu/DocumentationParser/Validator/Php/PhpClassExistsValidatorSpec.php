<?php

namespace spec\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpParser\Error;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Parser;
use PhpSpec\ObjectBehavior;
use Mamazu\DocumentationParser\Error\Error as DocError;

class PhpClassExistsValidatorSpec extends ObjectBehavior
{
    public function let(Parser $parser): void
    {
        $this->beConstructedWith(static function () { return true; }, $parser);
    }

    public function it_is_a_validator(): void
    {
        $this->shouldImplement(ValidatorInterface::class);
    }

    public function it_does_nothing_if_it_is_a_php_tag(Parser $parser, Block $block): void {
        $block->getContent()->willReturn('<?php');

        $parser->parse('<?php')->shouldBeCalled()->willReturn(null);

        $this->validate($block)->shouldReturn([]);
    }

    public function it_does_not_throw_an_exception_if_the_parser_returns_nothing(Parser $parser, Block $block): void {
        $block->getContent()->willReturn('');

        $parser->parse('<?php ')->shouldBeCalled()->willReturn(null);

        $this->validate($block)->shouldReturn([]);
    }

    public function it_validates_syntax_errors(Parser $parser, Block $block): void
    {
        $error= new Error('Syntax Error on line 10');

        $block->getContent()->willReturn('abc');
        $block->getType()->willReturn('php');
        $block->getFileName()->willReturn('some_file.php');
        $block->getRelativeLineNumber()->willReturn(10);

        $parser->parse('<?php abc')->shouldBeCalled()->willThrow($error);

        $result = $this->validate($block);
        $result->shouldBeArray();
        $result->shouldHaveCount(1);
        $result[0]->shouldHaveType(DocError::class);
    }

    public function it_returns_no_errors_if_the_class_was_found(
        Parser $parser,
        Block $block,
        Use_ $useStatement,
        UseUse $useObject
    ): void {
        $this->beConstructedWith(static function (string $className) { return $className === 'SomeClass'; }, $parser);

        $block->getType()->willReturn('php');
        $block->getFileName()->willReturn('some_file.php');
        $block->getContent()->willReturn('use SomeClass');
        $block->getRelativeLineNumber()->willReturn(10);

        $parser->parse('<?php use SomeClass')->shouldBeCalled()->willReturn([$useStatement]);

        $useStatement->uses = [$useObject];

        $useObject->name = 'SomeClass';
        $useObject->getLine()->willReturn(1);

        $array = $this->validate($block);
        $array->shouldBeArray();
        $array->shouldHaveCount(0);
    }
    public function it_returns_an_error_if_class_name_was_not_found(
        Parser $parser,
        Block $block,
        Use_ $useStatement,
        UseUse $useObject
    ): void {
        $this->beConstructedWith(static function (string $className) { return $className !== 'SomeClass'; }, $parser);

        $block->getType()->willReturn('php');
        $block->getFileName()->willReturn('some_file.php');
        $block->getContent()->willReturn('use SomeClass');
        $block->getRelativeLineNumber()->willReturn(10);

        $parser->parse('<?php use SomeClass')->willReturn([$useStatement]);

        $useStatement->uses = [$useObject];

        $useObject->name = 'SomeClass';
        $useObject->getLine()->willReturn(1);

        $array = $this->validate($block);
        $array->shouldBeArray();
        $array->shouldHaveCount(1);
    }
}
