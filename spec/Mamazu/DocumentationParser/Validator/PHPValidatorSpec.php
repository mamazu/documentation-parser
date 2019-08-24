<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\SystemAbstraction\CommandLineRunnerInterface;
use PhpSpec\ObjectBehavior;

class PHPValidatorSpec extends ObjectBehavior
{
    public function let(CommandLineRunnerInterface $commandLineRunner): void
    {
        $this->beConstructedWith($commandLineRunner);
    }

    // public function it_does_not_produce_an_error_if_the_file_is_valid(
    //     CommandLineRunnerInterface $commandLineRunner,
    //     Block $block
    // ) {
    //     $block->getContent()->willReturn('<?php echo "test"; ');
    //     $commandLineRunner->run('php -l /tmp/code.php 2>&1')->willReturn(['File is valid']);

    //     $this->validate($block)->shouldHaveCount(0);
    // }

    public function it_returns_an_error_if_the_command_fails(
        CommandLineRunnerInterface $commandLineRunner,
        Block $block
    ) {
        $block->getFileName()->willReturn('/dev/null');
        $block->getContent()->willReturn('<?php echo "test" ');
        $block->getRelativeLineNumber()->willReturn(10);

        $commandLineRunner->run('php -l /tmp/code.php 2>&1')
            ->willReturn(['PHP Parse error: Fatal error in /tmp/code.php on line 10']);

        $errors = $this->validate($block);
        $errors->shouldHaveCount(1);
        $errors[0]->getMessage()->shouldReturn('Fatal error');
        $errors[0]->getLineNumber()->shouldReturn(20);
    }
}
