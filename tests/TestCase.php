<?php

namespace Tests;

use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(UncompromisedVerifier::class)
            ->shouldReceive('verify')
            ->andReturn(true);
    }
}
