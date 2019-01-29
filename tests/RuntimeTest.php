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

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\Runtime
 */
final class RuntimeTest extends TestCase
{
    /**
     * @var \SebastianBergmann\Environment\Runtime
     */
    private $env;

    protected function setUp(): void
    {
        $this->env = new Runtime;
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testAbilityToCollectCodeCoverageCanBeAssessed(): void
    {
        $this->assertIsBool($this->env->canCollectCodeCoverage());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testBinaryCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getBinary());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanBeDetected(): void
    {
        $this->assertIsBool($this->env->isHHVM());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanBeDetected2(): void
    {
        $this->assertIsBool($this->env->isPHP());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testPCOVCanBeDetected(): void
    {
        $this->assertIsBool($this->env->hasPCOV());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testXdebugCanBeDetected(): void
    {
        $this->assertIsBool($this->env->hasXdebug());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testNameAndVersionCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getNameWithVersion());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testNameCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getName());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testNameAndCodeCoverageDriverCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getNameWithVersionAndCodeCoverageDriver());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testVersionCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getVersion());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testVendorUrlCanBeRetrieved(): void
    {
        $this->assertIsString($this->env->getVendorUrl());
    }
}
