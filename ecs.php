<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $configuration): void {
	$configuration
		->paths([
			__DIR__ . '/src',
			__DIR__ . '/tests',
			__DIR__ . '/ecs.php',
			__DIR__ . '/bin/doc-parser',
		]);
	$configuration->parallel(true);
	$configuration->indentation(Option::INDENTATION_TAB);

	$configuration->rule(DisallowLongArraySyntaxSniff::class);

	// run and fix, one by one
	$configuration->sets([
		SetList::SPACES,
		SetList::ARRAY,
		SetList::DOCBLOCK,
		SetList::PSR_12,
		SetList::STRICT,
		SetList::CLEAN_CODE,
		SetList::CONTROL_STRUCTURES,
	])
	;
};
