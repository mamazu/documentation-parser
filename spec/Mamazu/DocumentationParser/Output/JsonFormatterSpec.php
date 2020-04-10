<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Output;

use Mamazu\DocumentationParser\Error\Error;
use PhpSpec\ObjectBehavior;

class JsonFormatterSpec extends ObjectBehavior
{
    public function it_returns_nothing_if_there_is_no_error(): void
    {
        $this->format([])->shouldReturn('');
    }

    public function it_formats_the_an_error(): void
    {
        $errors = [new Error('some_file.php', 3, 'Unknown thing', 'yaml')];

        $this->format($errors)->shouldReturn(
            <<<JSON
[
    {
        "fileName": "some_file.php",
        "lineNumber": 3,
        "type": "yaml",
        "message": "Unknown thing"
    }
]
JSON
        );
    }

    public function it_formats_the_error_array(): void
    {
        $errors = [
            new Error('some_file.php', 3, 'Unknown thing'),
            new Error('some_file.php', 10, 'Error in format'),
        ];

        $this->format($errors)->shouldReturn(
            <<<JSON
[
    {
        "fileName": "some_file.php",
        "lineNumber": 3,
        "type": null,
        "message": "Unknown thing"
    },
    {
        "fileName": "some_file.php",
        "lineNumber": 10,
        "type": null,
        "message": "Error in format"
    }
]
JSON
        );
    }
}
