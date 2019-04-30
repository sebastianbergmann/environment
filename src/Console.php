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

final class Console
{
    /**
     * @var int
     */
    public const STDIN  = 0;

    /**
     * @var int
     */
    public const STDOUT = 1;

    /**
     * @var int
     */
    public const STDERR = 2;

    /**
     * Returns true if STDOUT supports colorization.
     *
     * This code has been copied and adapted from
     * Symfony\Component\Console\Output\StreamOutput.
     */
    public function hasColorSupport(): bool
    {
        if ('Hyper' === \getenv('TERM_PROGRAM')) {
            return true;
        }

        if ($this->isWindows()) {
            // @codeCoverageIgnoreStart
            return (\defined('STDOUT') && \function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(\STDOUT))
                || false !== \getenv('ANSICON')
                || 'ON' === \getenv('ConEmuANSI')
                || 'xterm' === \getenv('TERM');
            // @codeCoverageIgnoreEnd
        }

        if (!\defined('STDOUT')) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if ($this->isInteractive(\STDOUT)) {
            return true;
        }

        $stat = @\fstat(\STDOUT);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

    /**
     * Returns the number of columns of the terminal.
     *
     * @codeCoverageIgnore
     */
    public function getNumberOfColumns(): int
    {
        if ($this->isWindows()) {
            return $this->getNumberOfColumnsWindows();
        }

        if (!$this->isInteractive(\defined('STDIN') ? \STDIN : self::STDIN)) {
            return 80;
        }

        return $this->getNumberOfColumnsInteractive();
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     *
     * Normally, we want to use a resource as a parameter, yet sadly it's not always awailable,
     * eg when running code in interactive console (`php -a`), STDIN/STDOUT/STDERR constants are not defined.
     *
     * @param int|resource $fileDescriptor
     */
    public function isInteractive($fileDescriptor = self::STDOUT): bool
    {
        return (\is_resource($fileDescriptor) && \function_exists('stream_isatty') && @\stream_isatty($fileDescriptor)) // stream_isatty requires that descriptor is a real resource, not numeric ID of it
            || (\function_exists('posix_isatty') && @\posix_isatty($fileDescriptor));
    }

    private function isWindows(): bool
    {
        return \DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsInteractive(): int
    {
        if (\function_exists('shell_exec') && \preg_match('#\d+ (\d+)#', \shell_exec('stty size') ?: '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        if (\function_exists('shell_exec') && \preg_match('#columns = (\d+);#', \shell_exec('stty') ?: '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        return 80;
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsWindows(): int
    {
        $ansicon = \getenv('ANSICON');
        $columns = 80;

        if (\is_string($ansicon) && \preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', \trim($ansicon), $matches)) {
            $columns = $matches[1];
        } elseif (\function_exists('proc_open')) {
            $process = \proc_open(
                'mode CON',
                [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes,
                null,
                null,
                ['suppress_errors' => true]
            );

            if (\is_resource($process)) {
                $info = \stream_get_contents($pipes[1]);

                \fclose($pipes[1]);
                \fclose($pipes[2]);
                \proc_close($process);

                if (\preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches)) {
                    $columns = $matches[2];
                }
            }
        }

        return $columns - 1;
    }
}
