<?php
namespace Tests;

use App\Support\CurrentUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void { parent::setUp(); CurrentUser::set(null); }
    protected function tearDown(): void { CurrentUser::set(null); parent::tearDown(); }
}
