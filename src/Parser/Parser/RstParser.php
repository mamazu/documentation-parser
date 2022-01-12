<?php


namespace Mamazu\DocumentationParser\Parser\Parser;

use Gregwar\RST\Nodes\CodeNode;
use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Mamazu\DocumentationParser\Parser\Block;

class RstParser implements ParserInterface
{
	private Parser $rstParser;

	public function __construct(Parser $parser)
	{
		$this->rstParser = $parser;
	}

	public function canParse(string $fileName): bool
	{
		return strtolower(substr($fileName, -3)) === 'rst';
	}

	public function parse(string $fileName): array
	{
		$this->rstParser->getEnvironment()->getErrorManager()->abortOnError(false);
		$parsedOutput = $this->rstParser->parseFile($fileName);

		/** @var CodeNode[] $codeNodes */
		$codeNodes = $parsedOutput->getNodes(
			static function (Node $node): bool {
				return $node instanceof CodeNode;
			}
		);

		$blocks = [];
		foreach ($codeNodes as $codeNode) {
			if ($codeNode->getLanguage() === null) {
				continue;
			}

			$blocks[] = new Block(
				$fileName,
				$codeNode->getValue(),
				$codeNode->getStartingLineNumber(),
				$codeNode->getLanguage()
			);
		}

		return $blocks;
	}
}
