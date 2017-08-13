<?php

namespace Boxydev\Boxyback\Tests;

class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function testSimple()
    {
        $this->assertContains('a', 'a');
    }
}