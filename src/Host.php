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

use const PHP_OS_FAMILY;
use function assert;
use function function_exists;
use function getenv;
use function gethostname;
use function php_uname;
use function posix_geteuid;
use function posix_getpwuid;
use function trim;

final readonly class Host
{
    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        $candidate = gethostname();

        if ($candidate === false) {
            return 'unknown';
        }

        $candidate = trim($candidate);

        if ($candidate === '') {
            return 'unknown';
        }

        return $candidate;
    }

    /**
     * @return non-empty-string
     */
    public function user(): string
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $info = posix_getpwuid(posix_geteuid());

            if ($info !== false) {
                $candidate = trim($info['name']);
            }
        } elseif (PHP_OS_FAMILY === 'Windows') {
            $candidate = trim((string) getenv('USERNAME'));
        }

        if (!isset($candidate) || $candidate === '') {
            return 'unknown';
        }

        return $candidate;
    }

    /**
     * @return non-empty-string
     */
    public function operatingSystem(): string
    {
        $result = php_uname();

        assert($result !== '');

        return $result;
    }
}
