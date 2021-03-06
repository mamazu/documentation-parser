<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Bash;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class BashValidator implements ValidatorInterface {
    private const FILE_PATH = '/tmp/documentation-parser/caches.sh';

    /** @var string */
    private $pathToExecutor;

    public function __construct(string $pathToExecutor)
    {
        $this->pathToExecutor = $pathToExecutor;
    }

    public function validate(Block $block): array
    {
        @mkdir(dirname(self::FILE_PATH), 0777, true);
        file_put_contents(self::FILE_PATH, $block->getContent());
        $output = [];
        exec($this->pathToExecutor.' -n '.self::FILE_PATH.' 2>&1', $output);

        $errors = [];
        $executableName = basename(self::FILE_PATH);
        foreach($output as $line) {
            $matches = [];
            if (preg_match('/.*'.$executableName.':( line)? (\d+): (.*)/', $line, $matches) > 0) {
                $errors[] = Error::errorFromBlock($block, (int) $matches[2], $matches[3]);
            } else {
                $errors[] = Error::errorFromBlock($block, 0, $line);
            }
        }

        return $errors;
    }
}
