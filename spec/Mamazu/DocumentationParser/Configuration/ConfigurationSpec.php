<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Configuration;

use Mamazu\DocumentationParser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;

class ConfigurationSpec extends ObjectBehavior 
{
    public function let() {
        $this->beConstructedThrough('fromFile', [__DIR__.'/test_config.json']);
    }

    public function it_parses_the_files_array(): void
    {
        // todo: proper test
        $this->getFiles()->shouldHaveCount(1);
    }

    public function it_has_a_php_validator(): void
    {
        $this->getValidator('php')->shouldHaveType(ValidatorInterface::class);
    }

    public function it_throws_an_exception_on_getting_a_non_existing_validator(): void
    {
        $exception = new \InvalidArgumentException('No validator found for "python"');
        $this->shouldThrow($exception)->during('getValidator', ['python']);
    }

    public function it_has_a_parser(): void
    {
        $this->getParsers()->shouldHaveCount(1);
    }

    public function it_can_add_a_parser(ParserInterface $parser): void
    {
        $this->addParser('rst', $parser);

        $this->getParsers()->shouldHaveCount(2);
    }

    public function it_can_add_a_validator(ValidatorInterface $validator): void
    {
        $this->addValidator('python', $validator);

        $this->getValidator('python')->shouldReturn($validator);
    }
}