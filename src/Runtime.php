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

/**
 * Utility class for HHVM/PHP environment handling.
 */
final class Runtime
{
    /**
     * @var string
     */
    private static $binary;

    /**
     * Returns true when Xdebug or PCOV is available or
     * the runtime used is PHPDBG.
     */
    public function canCollectCodeCoverage(): bool
    {
        return $this->hasXdebug() || $this->hasPCOV() || $this->hasPHPDBGCodeCoverage();
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled, and is configured to discard comments.
     */
    public function discardsComments(): bool
    {
        if (!\extension_loaded('Zend OPcache')) {
            return false;
        }

        if (\ini_get('opcache.save_comments') !== '0') {
            return false;
        }

        if (\PHP_SAPI === 'cli' && \ini_get('opcache.enable_cli') === '1') {
            return true;
        }

        if (\PHP_SAPI !== 'cli' && \ini_get('opcache.enable') === '1') {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the binary of the current runtime.
     * Appends ' --php' to the path when the runtime is HHVM.
     */
    public function getBinary(): string
    {
        // HHVM
        if (self::$binary === null && $this->isHHVM()) {
            // @codeCoverageIgnoreStart
            if ((self::$binary = \getenv('PHP_BINARY')) === false) {
                self::$binary = \PHP_BINARY;
            }

            self::$binary = \escapeshellarg(self::$binary) . ' --php' .
                ' -d hhvm.php7.all=1';
            // @codeCoverageIgnoreEnd
        }

        if (self::$binary === null && \PHP_BINARY !== '') {
            self::$binary = \escapeshellarg(\PHP_BINARY);
        }

        if (self::$binary === null) {
            // @codeCoverageIgnoreStart
            $possibleBinaryLocations = [
                \PHP_BINDIR . '/php',
                \PHP_BINDIR . '/php-cli.exe',
                \PHP_BINDIR . '/php.exe',
            ];

            foreach ($possibleBinaryLocations as $binary) {
                if (\is_readable($binary)) {
                    self::$binary = \escapeshellarg($binary);

                    break;
                }
            }
            // @codeCoverageIgnoreEnd
        }

        if (self::$binary === null) {
            // @codeCoverageIgnoreStart
            self::$binary = 'php';
            // @codeCoverageIgnoreEnd
        }

        return self::$binary;
    }

    public function getNameWithVersion(): string
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    public function getNameWithVersionAndCodeCoverageDriver(): string
    {
        if (!$this->canCollectCodeCoverage() || $this->hasPHPDBGCodeCoverage()) {
            return $this->getNameWithVersion();
        }

        if ($this->hasXdebug()) {
            return \sprintf(
                '%s with Xdebug %s',
                $this->getNameWithVersion(),
                \phpversion('xdebug')
            );
        }

        if ($this->hasPCOV()) {
            return \sprintf(
                '%s with PCOV %s',
                $this->getNameWithVersion(),
                \phpversion('pcov')
            );
        }
    }

    public function getName(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'HHVM';
            // @codeCoverageIgnoreEnd
        }

        if ($this->isPHPDBG()) {
            // @codeCoverageIgnoreStart
            return 'PHPDBG';
            // @codeCoverageIgnoreEnd
        }

        return 'PHP';
    }

    public function getVendorUrl(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'http://hhvm.com/';
            // @codeCoverageIgnoreEnd
        }

        return 'https://secure.php.net/';
    }

    public function getVersion(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return HHVM_VERSION;
            // @codeCoverageIgnoreEnd
        }

        return \PHP_VERSION;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     */
    public function hasXdebug(): bool
    {
        return ($this->isPHP() || $this->isHHVM()) && \extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     */
    public function isHHVM(): bool
    {
        return \defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     */
    public function isPHP(): bool
    {
        return !$this->isHHVM() && !$this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     */
    public function isPHPDBG(): bool
    {
        return \PHP_SAPI === 'phpdbg' && !$this->isHHVM();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     *
     * @codeCoverageIgnore
     */
    public function hasPHPDBGCodeCoverage(): bool
    {
        return $this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with PCOV loaded and enabled
     */
    public function hasPCOV(): bool
    {
        return $this->isPHP() && \extension_loaded('pcov') && \ini_get('pcov.enabled');
    }
}
