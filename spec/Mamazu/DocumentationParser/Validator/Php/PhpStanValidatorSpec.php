<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

final class PhpStanValidatorSpec extends ObjectBehavior
{
    public function let(
        PhpCodeEnsurerInterface $codeEnsurer,
        Command $command,
        InputInterface $input,
        BufferedOutput $output
    ): void {
        $this->beConstructedWith($codeEnsurer, $command, $input, $output);
    }

    public function it_is_a_validator(): void
    {
        $this->shouldImplement(ValidatorInterface::class);
    }

    public function it_works_with_default_parameters(
        PhpCodeEnsurerInterface $codeEnsurer,
        Command $command,
        BufferedOutput $output
    ): void {
        $this->beConstructedWith($codeEnsurer, $command, null, $output);

        $output->fetch()->shouldBeCalled()->willReturn(
            <<<TXT
Configuration file used

{"totals": {"file_errors": 0}, "files": {}}
TXT
        );

        $this->validate(new Block('abc', '<?php test', 1, 'php'))->shouldBe([]);
    }

    public function it_validates_a_block_with_php_code(
        PhpCodeEnsurerInterface $codeEnsurer,
        Command $command,
        InputInterface $input,
        BufferedOutput $output
    ): void {
        $codeEnsurer->putPhpCodeToFile('<?php test', '/tmp/documentation-parser/cache.php');

        $command->run($input, $output)->shouldBeCalled();

        $output->fetch()->shouldBeCalled()->willReturn(
            <<<TXT
Configuration file used

{"totals": {"file_errors": 0}, "files": {}}
TXT
        );

        $this->validate(new Block('abc', '<?php test', 1, 'php'))->shouldBe([]);
    }

    public function it_validates_a_block_with_errors(
        PhpCodeEnsurerInterface $codeEnsurer,
        Command $command,
        InputInterface $input,
        BufferedOutput $output
    ): void {
        $codeEnsurer->putPhpCodeToFile('test', '/tmp/documentation-parser/cache.php');

        $command->run($input, $output)->shouldBeCalled();

        $output->fetch()->shouldBeCalled()->willReturn(
            <<<TXT
Configuration file used

{"totals": {"file_errors": 1}, "files": {"abc": {"messages": [{"message": "Error", "line": 10}]}}}
TXT
        );

        $errors = $this->validate(new Block('abc', 'test', 1, 'php'));
        $errors->shouldHaveCount(1);
        $errors[0]->shouldBeLike(new Error('abc', 11, '[php] Error'));
    }
}
