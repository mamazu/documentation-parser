<?php

namespace spec\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\CompositeValidator;
use Mamazu\DocumentationParser\Validator\Error;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompositeValidatorSpec extends ObjectBehavior
{
    public function it_is_a_validator(): void
    {
        $this->beConstructedWith([], true);
        $this->shouldImplement(ValidatorInterface::class);
    }

    public function it_validates_with_multiple_validators(
        ValidatorInterface $validator1,
        ValidatorInterface $validator2,
        ValidatorInterface $validator3,
        Block $block
    ): void {
        $this->beConstructedWith([$validator1, $validator2, $validator3], true);

        $validator1->validate($block)->willReturn([new Error('', 1, '')]);
        $validator2->validate($block)->willReturn([new Error('abc', 2, '')]);
        $validator3->validate($block)->willReturn([]);

        $result = $this->validate($block);
        $result->shouldBeArray();
        $result->shouldHaveCount(2);
    }

    public function it_validates_only_first_validator_if_it_should_stop_on_error(
        ValidatorInterface $validator1,
        ValidatorInterface $validator2,
        Block $block
    ): void {
        $this->beConstructedWith([$validator1, $validator2], false);

        $validator1->validate($block)->willReturn([new Error('', 1, '')]);
        $validator2->validate($block)->shouldNotBeCalled();

        $result = $this->validate($block);
        $result->shouldBeArray();
        $result->shouldHaveCount(1);
    }
}
