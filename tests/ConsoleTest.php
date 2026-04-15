<?php declare(strict_types=1);
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

use const PHP_BINARY;
use function assert;
use function fclose;
use function fopen;
use function is_resource;
use function proc_close;
use function proc_open;
use function stream_get_contents;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

#[CoversClass(Console::class)]
final class ConsoleTest extends TestCase
{
    #[Ticket('https://github.com/sebastianbergmann/environment/issues/79')]
    public function testIsInteractiveReturnsFalseForPipeResource(): void
    {
        $process = proc_open(
            'echo hello',
            [1 => ['pipe', 'w']],
            $pipes,
        );

        $this->assertIsResource($process);

        assert(isset($pipes[1]) && is_resource($pipes[1]));

        try {
            $this->assertFalse((new Console)->isInteractive($pipes[1]));
        } finally {
            stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
        }
    }

    #[Ticket('https://github.com/sebastianbergmann/environment/issues/79')]
    public function testIsInteractiveReturnsFalseForRegularFileResource(): void
    {
        $resource = fopen(__FILE__, 'r');

        $this->assertNotFalse($resource);

        try {
            $this->assertFalse((new Console)->isInteractive($resource));
        } finally {
            fclose($resource);
        }
    }

    #[Ticket('https://github.com/sebastianbergmann/environment/issues/79')]
    #[RequiresOperatingSystemFamily('Linux')]
    public function testGetNumberOfColumnsDoesNotProduceStderrWarningsInNonTtyEnvironment(): void
    {
        $process = proc_open(
            [
                PHP_BINARY,
                '-r',
                'require __DIR__ . "/vendor/autoload.php"; echo (new SebastianBergmann\Environment\Console)->getNumberOfColumns();',
            ],
            [
                0 => ['file', '/dev/null', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            __DIR__ . '/..',
        );

        $this->assertIsResource($process);

        assert(isset($pipes[1]) && is_resource($pipes[1]));
        assert(isset($pipes[2]) && is_resource($pipes[2]));

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $this->assertSame('80', $stdout);
        $this->assertSame('', $stderr);
    }
}
