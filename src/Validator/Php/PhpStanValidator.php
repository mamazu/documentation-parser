<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurer;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

final class PhpStanValidator implements ValidatorInterface
{
    private const FILE_PATH = '/tmp/documentation-parser/cache.php';

    /** @var PhpCodeEnsurerInterface */
    private $codeEnsurer;

    public function __construct(
        ?PhpCodeEnsurerInterface $codeEnsurer = null
    ) {
        $this->codeEnsurer = $codeEnsurer ?? new PhpCodeEnsurer();
    }

    public function validate(Block $block): array
    {
        $this->codeEnsurer->putPhpCodeToFile($block->getContent(), self::FILE_PATH);

        $filePath = self::FILE_PATH;
        $phpStanPath = __DIR__ . '/../../../vendor/bin/phpstan';
        exec("$phpStanPath analyse $filePath --error-format=json --no-progress", $output);

        return array_map(
            static function (array $error) use ($block): Error {
                return Error::errorFromBlock($block, (int) ($error['line'] ?? 0), $error['message']);
            },
            $this->parseOutput($output)
        );
    }

    /**
     * @param array<string> $output
     *
     * @return array<array<string>>
     */
    private function parseOutput(array $output): array
    {
        $result = [];
        foreach ($output as $line) {
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
