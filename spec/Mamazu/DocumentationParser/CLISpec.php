<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\CLI;
use Mamazu\DocumentationParser\FileList;
use PhpSpec\ObjectBehavior;

class CLISpec extends ObjectBehavior
{
    public function it_parses_a_files_only_parameters(FileList $fileList): void
    {
        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();

        $this->beConstructedWith($fileList, ['abc.php', 'cde.md']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludeFile()->shouldReturn(null);
    }

    public function it_parses_a_file_an_include_file_mix(FileList $fileList): void
    {

        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();
        $fileList->addFile('include.php')->shouldNotBeCalled();

        $this->beConstructedWith($fileList, ['abc.php', '-i', 'include.php', 'cde.md']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludeFile()->shouldReturn('include.php');
    }

    public function it_has_no_include_file_if_no_value_was_given(FileList $fileList): void
    {

        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();

        $this->beConstructedWith($fileList, ['abc.php', 'cde.md', '-i']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludeFile()->shouldReturn(null);
    }
}
