<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication
 * @mixin \Illuminate\Foundation\Testing\Concerns\MakesHttpRequests
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithConsole
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithContainer
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithSession
 */
abstract class TestCase extends BaseTestCase
{
    //
}
