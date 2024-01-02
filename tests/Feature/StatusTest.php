<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_homepage_contains_empty_table(): void
    {
        $response = $this->get('/status');

        $response->assertStatus(200);
    }
}
