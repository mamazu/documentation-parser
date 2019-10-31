<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\Php;

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Error;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Parser;
use Webmozart\Assert\Assert;
use function array_merge;

final class PhpClassExistsValidator implements ValidatorInterface
{
    /** @var Parser */
    private $parser;

    /** @var callable */
    private $classExists;

    public function __construct(Parser $parser, callable $classExists)
    {
        $this->parser = $parser;
        $this->classExists = $classExists;
    }

    private function getPHPCode(Block $block): string
    {
        $sourceCode = $block->getContent();
        if (strpos($sourceCode, '<?php') === false) {
            return '<?php '.$sourceCode;
        }

        return $sourceCode;
    }

    /** {@inheritDoc} */
    public function validate(Block $block): array
    {
        try {
            $statements = $this->parser->parse($this->getPHPCode($block));
            Assert::notNull($statements);
        } catch (\Throwable $exception) {
            return $this->processSyntaxErrors($block, $exception->getMessage());
        }

        $errors = [];
        foreach ($statements as $statement) {
            if ($statement instanceof Use_) {
                /** @var array<UseUse> $useObject */
                $useObject = $statement->uses;
                $errors = array_merge($errors, $this->validateUseStatement($block, $useObject));
            }
        }

        return $errors;
    }

    private function processSyntaxErrors(Block $block, string $message): array
    {
        $matches = [];
        if(preg_match('/(.*) on line (\d+)/si', $message, $matches) === 0) {
            return [];
        }

        $message = $matches[1];
        $lineNumber = (int) $matches[2];

        return [Error::errorFromBlock($block, $lineNumber, $message)];

    }

    private function validateUseStatement(Block $block, array $useObject): array
    {
        $errors = [];
        foreach ($useObject as $useStatement) {
            /** @var UseUse $useStatement */
            $className = (string) $useStatement->name;
            $classExistenceChecker = $this->classExists;
            $classExists = $classExistenceChecker($className);
            if (!$classExists) {
                $errors[] = Error::errorFromBlock($block, $useStatement->getLine(), 'Unknown class: '.$className);
            }
        }

        return $errors;
    }

}
