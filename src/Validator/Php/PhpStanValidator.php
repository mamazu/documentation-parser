<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Php;

use _HumbugBoxfac515c46e83\Symfony\Component\Console\Input\ArrayInput;
use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurer;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PHPStan\Command\AnalyseCommand;
use _HumbugBoxfac515c46e83\Symfony\Component\Console\Input\InputInterface;
use _HumbugBoxfac515c46e83\Symfony\Component\Console\Output\BufferedOutput;

final class PhpStanValidator implements ValidatorInterface
{
    private const FILE_PATH = '/tmp/documentation-parser/cache.php';

    /** @var AnalyseCommand */
    private $command;

    /** @var InputInterface */
    private $input;

    /** @var BufferedOutput */
    private $output;

    /** @var PhpCodeEnsurerInterface */
    private $codeEnsurer;

    public function __construct(
        ?PhpCodeEnsurerInterface $codeEnsurer = null,
        ?AnalyseCommand $command = null,
        ?InputInterface $input = null,
        ?BufferedOutput $output = null
    ) {
        $this->command     = $command ?? new AnalyseCommand([]);
        $definition = $this->command->getDefinition();

        $this->input       = $input ?? new ArrayInput(
                [
                    'paths'          => [self::FILE_PATH],
                    '--error-format' => 'json',
                ],
                $definition
            );
        $this->output      = $output ?? new BufferedOutput();
        $this->codeEnsurer = $codeEnsurer ?? new PhpCodeEnsurer();
    }

    public function validate(Block $block): array
    {
        $this->codeEnsurer->putPhpCodeToFile($block->getContent(), self::FILE_PATH);

        $this->command->run($this->input, $this->output);

        $parsed = $this->parseOutput($this->output->fetch());

        return array_map(
            static function (array $error) use ($block): Error {
                return Error::errorFromBlock($block, (int) ($error['line'] ?? 0), $error['message']);
            },
            $parsed
        );
    }

    /** @return array<array<string>> */
    private function parseOutput(string $output): array
    {
        $lines  = explode("\n", $output);
        $result = [];
        foreach ($lines as $line) {
            if (($line[0] ?? '') === '{') {
                $result = json_decode($line, true);
            }
        }

        if (($result['totals']['file_errors'] ?? 0) > 0) {
            // We only take the first file here as we are dealing with code blocks one by one so there is only ever one file analysed
            return array_values($result['files'])[0]['messages'];
        }

        return [];
    }
}
