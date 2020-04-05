<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser\Utils;


use InvalidArgumentException;
use Mamazu\DocumentationParser\Utils\PhpCodeEnsurerInterface;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;

final class PhpCodeEnsurerSpec extends ObjectBehavior
{
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

    public function it_puts_the_php_code_in_a_file(): void
    {
        $this->putPhpCodeToFile('<?php', '/tmp/hello.php');
    }

    public function it_throws_an_error_if_the_directory_can_not_be_created(): void
    {
        vfsStream::setup('workDir');
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('putPhpCodeToFile', ['<?php', 'vfs://workdir/hello.php']);
    }
}