<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\CLI;
use Mamazu\DocumentationParser\FileList;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CLITest extends TestCase
{
	private vfsStreamDirectory $workDir;

	private CLI $cLI;

	protected function setUp(): void
	{
		$this->workDir = vfsStream::setup('workDir');
	}

	public function testParsesAFilesOnlyParameters(): void
	{
		$matcher = $this->exactly(2);
		/** @var FileList|MockObject $fileList */
		$fileList = $this->createMock(FileList::class);
		$fileList->expects($matcher)
			->method('addFile')
			->willReturnCallback(function (...$args) use ($matcher) {
				$expected = [
					['abc.php'],
					['cde.md'],
				];
				$this->assertEquals($expected[$matcher->numberOfInvocations() - 1], $args);
			});

		$this->cLI = new CLI($fileList, ['abc.php', 'cde.md']);
		$this->assertSame($fileList, $this->cLI->getFilesToParse());
		$this->assertSame([], $this->cLI->getIncludePaths());
	}

	public function testParsesAFileAnIncludeFileMix(): void
	{
		$matcher = $this->exactly(2);
		/** @var FileList|MockObject $fileList */
		$fileList = $this->createMock(FileList::class);
		$fileList->expects($matcher)
			->method('addFile')
			->willReturnCallback(function (...$args) use ($matcher) {
				$expected = [
					['abc.php'],
					['cde.md'],
				];
				$this->assertEquals($expected[$matcher->numberOfInvocations() - 1], $args);
			});

		$this->workDir->addChild(vfsStream::newFile('bananas.php'));
		$this->cLI = new CLI($fileList, ['abc.php', '-i', 'vfs://workDir/bananas.php', 'cde.md']);
		$this->assertSame($fileList, $this->cLI->getFilesToParse());
		$this->assertSame(['vfs://workDir/bananas.php'], $this->cLI->getIncludePaths());
	}

	public function testHasNoIncludeFileIfNoValueWasGiven(): void
	{
		$matcher = $this->exactly(2);
		/** @var FileList|MockObject $fileList */
		$fileList = $this->createMock(FileList::class);
		$fileList->expects($matcher)
			->method('addFile')
			->willReturnCallback(function (...$args) use ($matcher) {
				$expected = [
					['abc.php'],
					['cde.md'],
				];
				$this->assertEquals($expected[$matcher->numberOfInvocations() - 1], $args);
			});

		$this->cLI = new CLI($fileList, ['abc.php', 'cde.md', '-i']);
		$this->assertSame($fileList, $this->cLI->getFilesToParse());
		$this->assertSame([], $this->cLI->getIncludePaths());
	}
}
