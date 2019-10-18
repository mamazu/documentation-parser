<?php

namespace spec\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Error;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Parser;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;

class PhpClassExistsValidatorSpec extends ObjectBehavior
{
    public function let(Parser $parser): void
    {
        $this->beConstructedWith($parser, function () { return true; });
    }

    function it_is_a_validator(): void
    {
        $this->shouldImplement(ValidatorInterface::class);
    }

    public function it_returns_no_errors_if_the_class_was_found(
        Parser $parser,
        Block $block,
        Use_ $useStatement,
        UseUse $useObject
    ): void {
        $this->beConstructedWith($parser, static function (string $className) { return $className === 'SomeClass'; });

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
        $array->shouldHaveCount(0);
    }
    public function it_returns_an_error_if_class_name_was_not_found(
        Parser $parser,
        Block $block,
        Use_ $useStatement,
        UseUse $useObject
    ): void {
        $this->beConstructedWith($parser, static function (string $className) { return $className !== 'SomeClass'; });

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
