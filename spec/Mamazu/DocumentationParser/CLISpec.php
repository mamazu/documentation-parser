<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\CLI;
use Mamazu\DocumentationParser\FileList;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

class CLISpec extends ObjectBehavior
{
    /** @var vfsStreamDirectory */
    private vfsStreamDirectory $workDir;

    public function let(): void
    {
        $this->workDir = vfsStream::setup('workDir');
    }

    public function it_parses_a_files_only_parameters(FileList $fileList): void
    {
        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();

        $this->beConstructedWith($fileList, ['abc.php', 'cde.md']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludePaths()->shouldReturn([]);
    }

    public function it_parses_a_file_an_include_file_mix(FileList $fileList): void
    {

        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();
        $fileList->addFile('include.php')->shouldNotBeCalled();

        $this->workDir->addChild(vfsStream::newFile('bananas.php'));

        $this->beConstructedWith($fileList, ['abc.php', '-i', 'vfs://workDir/bananas.php', 'cde.md']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludePaths()->shouldReturn(['vfs://workDir/bananas.php']);
    }

    public function it_has_no_include_file_if_no_value_was_given(FileList $fileList): void
    {

        $fileList->addFile('abc.php')->shouldBeCalled();
        $fileList->addFile('cde.md')->shouldBeCalled();

        $this->beConstructedWith($fileList, ['abc.php', 'cde.md', '-i']);

        $this->getFilesToParse()->shouldReturn($fileList);
        $this->getIncludePaths()->shouldReturn([]);
    }

}
