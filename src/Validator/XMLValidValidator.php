<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Validator;

use DOMDocument;
use LibXMLError;
use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\Error;

final class XMLValidValidator implements ValidatorInterface
{

        /** {@inheritDoc} */
    public function validate(Block $block): array
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument('1.0', 'uft-s');
        $doc->loadXML($block->getContent());

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return array_map(static function(LibXMLError $error) use ($block) : Error {
            return Error::errorFromBlock($block, $error->line, trim($error->message));
        }, $errors);
    }
}
