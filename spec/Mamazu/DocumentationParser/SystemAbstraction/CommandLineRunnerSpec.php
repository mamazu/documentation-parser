<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\SystemAbstraction;

use Exception;
use PhpSpec\ObjectBehavior;

class CommandLineRunnerSpec extends ObjectBehavior
{ 
    public function let(): void {
        $this->beConstructedWith(false);
    }

    public function it_executes_a_command_and_returns_the_output(): void
    {
        $this->run('echo test')->shouldReturn(['test']);
    }

    public function it_throws_an_exception_if_the_program_fails(): void
    {
        $this->beConstructedWith(true);
        $this->shouldThrow(Exception::class)->during('run', ['return 1']);
    }
}
