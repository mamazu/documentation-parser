<?php

declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Utils;

use InvalidArgumentException;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class PhpCodeEnsurerSpec extends ObjectBehavior
{
	public function let(Filesystem $filesystem): void
	{
		$this->beConstructedWith($filesystem);
	}

	public function it_is_a_code_ensurer(): void
	{
		$this->shouldImplement(PhpCodeEnsurerInterface::class);
	}

	public function it_prefixes_php_code(): void
	{
		$this->getPHPCode('echo "Hello";')->shouldReturn('<?php echo "Hello";');
	}

	public function it_leaves_php_code_untouched(): void
	{
		$this->getPHPCode(' <?php print_r(["Hello"]);')->shouldReturn('<?php print_r(["Hello"]);');
	}

	public function it_puts_the_php_code_in_a_file(Filesystem $filesystem): void
	{
		$filesystem->dumpFile('/tmp/hello.php', '<?php')->shouldBeCalled();

		$this->putPhpCodeToFile('<?php', '/tmp/hello.php');
	}

	public function it_throws_an_error_if_the_directory_can_not_be_created(Filesystem $filesystem): void
	{
		$filesystem
			->dumpFile('hello.php', '<?php')
			->shouldBeCalled()
			->willThrow(new IOException('Could not create directory'));

		$this
			->shouldThrow(IOException::class)
			->during('putPhpCodeToFile', ['<?php', 'hello.php']);
	}
}
