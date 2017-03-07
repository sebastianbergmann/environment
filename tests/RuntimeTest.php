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

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\Runtime
 */
class RuntimeTest extends TestCase
{
    /**
     * @var \SebastianBergmann\Environment\Runtime
     */
    private $env;

    protected function setUp()
    {
        $this->env = new Runtime;
    }

    public function testAbilityToCollectCodeCoverageCanBeAssessed()
    {
        $this->assertInternalType('boolean', $this->env->canCollectCodeCoverage());
    }

    public function testBinaryCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getBinary());
    }

    public function testCanBeDetected()
    {
        $this->assertInternalType('boolean', $this->env->isHHVM());
    }

    public function testCanBeDetected2()
    {
        $this->assertInternalType('boolean', $this->env->isPHP());
    }

    public function testXdebugCanBeDetected()
    {
        $this->assertInternalType('boolean', $this->env->hasXdebug());
    }

    public function testNameAndVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getNameWithVersion());
    }

    public function testNameCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getName());
    }

    public function testVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getVersion());
    }

    public function testVendorUrlCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getVendorUrl());
    }
}
