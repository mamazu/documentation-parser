<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator;

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

    public function getLineNumber(): void
    {
        $this->getLineNumber()->shouldReturn(10);
    }

    public function getFileName(): void
    {
        $this->getFileName()->shouldReturn('some_filename.php');
    }
}