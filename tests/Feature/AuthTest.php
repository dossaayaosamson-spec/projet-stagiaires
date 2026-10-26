<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_dynamic_stats()
    {
        Book::factory()->count(3)->create();
        User::factory()->count(5)->create();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Ouvrages');
        $response->assertSee('Lecteurs');
        $response->assertSee('+3');
        $response->assertSee('+5');
    }
}
