<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang di');
        $response->assertSee('Pengadilan Negeri Natuna Kelas II');
        $response->assertSee('Pengisian Buku Tamu');
        $response->assertSee('Survey Kepuasan Masyarakat');
        $response->assertSee('Survey IKM dan SPAK');
    }
}
