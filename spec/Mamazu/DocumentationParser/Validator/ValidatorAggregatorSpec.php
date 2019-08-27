<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Error;
use Mamazu\DocumentationParser\Validator\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorAggregatorSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith([]);
    }

    public function it_is_a_parser(): void
    {
        $this->beAnInstanceOf(ValidatorInterface::class);
    }

    public function it_can_add_a_validator(ValidatorInterface $validator): void
    {
        $this->addValidator('php', $validator);
    }

    public function it_delegates_the_validation(
        ValidatorInterface $validator1,
        ValidatorInterface $validator2,
        Block $block
    ) {
        $block->getType()->willReturn('php');

        $this->beConstructedWith(['php' => $validator1, 'python' => $validator2]);

        $validator1->validate($block)->willReturn([new Error('hello.md', 0, 'Error')]);
        $validator2->validate(Argument::any())->shouldNotBeCalled();

        $this->validate($block)->shouldHaveCount(1);
    }
    
    public function it_returns_no_errors_if_the_validator_is_not_defined(Block $block): void
    {
        $block->getType()->willReturn('php');

        $this->validate($block)->shouldReturn([]);
    }
}
