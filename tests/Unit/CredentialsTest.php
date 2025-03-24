<?php

namespace Tests\Unit;

use FireAPI\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    public function testConstructorSetsCorrectBaseUrlForSandbox()
    {
        $credentials = new Credentials('test-token', true);
        $this->assertEquals('https://sandbox.fireapi.de/', $credentials->baseUrl);
    }

    public function testConstructorSetsCorrectBaseUrlForLive()
    {
        $credentials = new Credentials('test-token', false);
        $this->assertEquals('https://live.fireapi.de/', $credentials->baseUrl);
    }
}