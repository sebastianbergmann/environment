<?php
/*
 * This file is part of the Environment package.
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
class Runtime
{
    /**
     * @var string
     */
    private static $binary;

    /**
     * Returns true when Xdebug is supported or
     * the runtime used is PHPDBG (PHP >= 7.0).
     *
     * @return bool
     */
    public function canCollectCodeCoverage()
    {
        return $this->hasXdebug() || $this->hasPHPDBGCodeCoverage();
    }

    /**
     * Returns the path to the binary of the current runtime.
     * Appends ' --php' to the path when the runtime is HHVM.
     *
     * @return string
     */
    public function getBinary()
    {
        // HHVM
        if (self::$binary === null && $this->isHHVM()) {
            // @codeCoverageIgnoreStart
            if ((self::$binary = getenv('PHP_BINARY')) === false) {
                self::$binary = PHP_BINARY;
            }

            self::$binary = escapeshellarg(self::$binary) . ' --php';
            // @codeCoverageIgnoreEnd
        }

        if (PHP_BINARY !== '') {
            self::$binary = escapeshellarg(PHP_BINARY);
        }

        if (self::$binary === null) {
            // @codeCoverageIgnoreStart
            $possibleBinaryLocations = [
                PHP_BINDIR . '/php',
                PHP_BINDIR . '/php-cli.exe',
                PHP_BINDIR . '/php.exe'
            ];

            foreach ($possibleBinaryLocations as $binary) {
                if (is_readable($binary)) {
                    self::$binary = escapeshellarg($binary);
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

    /**
     * @return string
     */
    public function getNameWithVersion()
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'HHVM';
            // @codeCoverageIgnoreEnd
        } elseif ($this->isPHPDBG()) {
            // @codeCoverageIgnoreStart
            return 'PHPDBG';
            // @codeCoverageIgnoreEnd
        } else {
            return 'PHP';
        }
    }

    /**
     * @return string
     */
    public function getVendorUrl()
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'http://hhvm.com/';
            // @codeCoverageIgnoreEnd
        } else {
            return 'https://secure.php.net/';
        }
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return HHVM_VERSION;
            // @codeCoverageIgnoreEnd
        } else {
            return PHP_VERSION;
        }
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     *
     * @return bool
     */
    public function hasXdebug()
    {
        return ($this->isPHP() || $this->isHHVM()) && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     *
     * @return bool
     */
    public function isHHVM()
    {
        return defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     *
     * @return bool
     */
    public function isPHP()
    {
        return !$this->isHHVM() && !$this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     *
     * @return bool
     */
    public function isPHPDBG()
    {
        return PHP_SAPI === 'phpdbg' && !$this->isHHVM();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     *
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function hasPHPDBGCodeCoverage()
    {
        return $this->isPHPDBG() && function_exists('phpdbg_start_oplog');
    }
}
