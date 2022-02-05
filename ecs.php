<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set(Option::PATHS, [
		__DIR__ . '/src',
		__DIR__ . '/spec',
		__DIR__ . '/ecs.php',
		__DIR__ . '/bin/doc-parser',
	]);
	$parameters->set(Option::PARALLEL, true);
	$parameters->set(Option::INDENTATION, 'tab');

	$services = $containerConfigurator->services();
	$services->set(ArraySyntaxFixer::class)
		->call('configure', [[
			'syntax' => 'short',
		]]);

	// run and fix, one by one
	$containerConfigurator->import(SetList::SPACES);
	$containerConfigurator->import(SetList::ARRAY);
	$containerConfigurator->import(SetList::DOCBLOCK);
	$containerConfigurator->import(SetList::PSR_12);
	$containerConfigurator->import(SetList::STRICT);
	$containerConfigurator->import(SetList::CLEAN_CODE);
	$containerConfigurator->import(SetList::CONTROL_STRUCTURES);
};
