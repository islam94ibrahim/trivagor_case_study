<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as Test;

abstract class TestCase extends Test
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
