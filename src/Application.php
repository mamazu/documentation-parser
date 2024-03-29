<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Output\FormatterInterface;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Parser\Parser\ParserInterface;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

class Application
{
	/**
	 * @var array<ParserInterface>
	 */
	private array $parser;

	/**
	 * @var array<ValidatorInterface>
	 */
	private array $validator;

	/**
	 *  @param array<ParserInterface> $parser
	 *  @param array<ValidatorInterface> $validator
	 */
	public function __construct(array $parser, array $validator)
	{
		$this->parser = $parser;
		$this->validator = $validator;
	}

	/**
	 * @return array<ParserInterface>
	 */
	public function &getParser(): array
	{
		return $this->parser;
	}

	/**
	 * @return array<ValidatorInterface>
	 */
	public function &getValidator(): array
	{
		return $this->validator;
	}

	/**
	 * @return array<Error>
	 */
	public function parse(FileList $fileList): array
	{
		$validationErrors = [];
		foreach ($fileList->getAllValidFiles() as $fileName) {
			$blocks = $this->getDocumentationBlocks($fileName);
			foreach ($blocks as $block) {
				$errors = $this->validateBlock($block);
				$validationErrors = array_merge($validationErrors, $errors);
			}
		}

		return $validationErrors;
	}

	public function run(FormatterInterface $formatter, FileList $fileList): void
	{
		try {
			$output = $this->parse($fileList);
			echo $formatter->format($output);
		} catch (\Throwable $throwable) {
			fwrite(STDERR, $throwable->getMessage());
			echo $throwable->getTraceAsString();
			exit($throwable->getCode());
		}

		if (count($output) > 0) {
			exit(1);
		}
	}

	/**
	 * @return array<Block>
	 */
	private function getDocumentationBlocks(string $fileName): array
	{
		foreach ($this->parser as $parser) {
			if ($parser->canParse($fileName)) {
				return $parser->parse($fileName);
			}
		}

		trigger_error('There was no parser found for file ' . $fileName, E_USER_WARNING);

		return [];
	}

	/**
	 * @return array<Error>
	 */
	private function validateBlock(Block $block): array
	{
		$type = $block->getType();

		if (! array_key_exists($type, $this->validator)) {
			trigger_error('No validator for type: ' . $type . ' @ ' . $block->getFileName() . ':' . $block->getRelativeLineNumber(), E_USER_WARNING);
			return [];
		}

		$handler = $this->validator[$type];

		return $handler->validate($block);
	}
}
