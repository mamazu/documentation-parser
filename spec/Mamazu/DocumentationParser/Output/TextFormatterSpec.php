<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use PhpSpec\ObjectBehavior;

class TextFormatterSpec extends ObjectBehavior
{
	public function it_formats_the_an_error(): void
	{
		$errors = [new Error('some_file.php', 3, 'Unknown thing')];

		$this->format($errors)->shouldReturn('some_file.php:3 ---- Unknown thing');
	}

	public function it_formats_the_error_array(): void
	{
		$errors = [
			new Error('some_file.php', 3, 'Unknown thing'),
			new Error('some_file.php', 10, 'Error in format'),
		];

		$this->format($errors)->shouldReturn("some_file.php:3 ---- Unknown thing\nsome_file.php:10 ---- Error in format");
	}
}
