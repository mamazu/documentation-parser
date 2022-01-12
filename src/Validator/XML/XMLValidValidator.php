<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator\XML;

use DOMDocument;
use LibXMLError;
use Mamazu\DocumentationParser\Error\Error;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\ValidatorInterface;

final class XMLValidValidator implements ValidatorInterface
{
	public function validate(Block $block): array
	{
		libxml_use_internal_errors(true);

		$doc = new DOMDocument('1.0', 'uft-s');
		$doc->loadXML($block->getContent());

		$errors = libxml_get_errors();
		libxml_clear_errors();

		return array_map(static function (LibXMLError $error) use ($block): Error {
			return Error::errorFromBlock($block, $error->line, trim($error->message));
		}, $errors);
	}
}
