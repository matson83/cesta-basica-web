<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Evita depender dos assets compilados (public/build/manifest.json)
        // ao renderizar views nos testes de feature.
        $this->withoutVite();
    }
}
