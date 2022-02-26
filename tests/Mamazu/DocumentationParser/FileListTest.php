<?php

declare(strict_types=1);
namespace Tests\Mamazu\DocumentationParser;

use Mamazu\DocumentationParser\FileList;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class FileListTest extends TestCase
{
	private vfsStreamDirectory $workDir;

	private FileList $fileList;

	protected function setUp(): void
	{
		$this->fileList = new FileList();
		$this->workDir = vfsStream::setup('workDir');
	}

	public function testRemovesAFile(): void
	{
		$this->fileList->addFile('abc.md');
		$this->fileList->addFile('bcd.md');
		$this->fileList->removeFile('abc.md');

		$this->assertSame(['bcd.md'], iterator_to_array($this->fileList->getAllFiles()));
	}

	public function testGetsAllValidFiles(): void
	{
		$this->workDir->addChild(vfsStream::newFile('test.php'));
		$this->fileList->addFile('vfs://workDir/test.php');
		$this->fileList->addFile('vfs://workDir/bananas.php');

		$this->expectWarning();
		$this->expectWarningMessage('Could not find file: vfs://workDir/bananas.php');

		$this->fileList->getAllValidFiles();
	}

	public function testAddsAFile(): void
	{
		$this->fileList->addFile('abc.md');
		$this->assertSame(['abc.md'], iterator_to_array($this->fileList->getAllFiles()));
	}

	public function testAddsADirectory(): void
	{
		$directory = vfsStream::newDirectory('abc');
		$directory->addChild(vfsStream::newFile('testing.php'));
		$this->workDir->addChild($directory);
		$this->fileList->addFile('vfs://workDir/abc');
		$this->assertSame(['vfs://workDir/abc/testing.php'], iterator_to_array($this->fileList->getAllFiles()));
	}
}
