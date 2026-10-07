<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\PortfolioOwnerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman depan portal kampus untuk umum.
 */
class PortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_general_portal_instead_of_personal_portfolio(): void
    {
        $this->seed(PortfolioOwnerSeeder::class);
        $owner = User::where('email', config('portfolio.owner_email'))->firstOrFail();

        $this->get('/')
            ->assertOk()
            ->assertSee('Portal Portofolio Akademik Mahasiswa')
            ->assertSee('Masuk ke Portal')
            ->assertDontSee($owner->bio);
    }

    public function test_portal_shows_statistics_without_sensitive_data(): void
    {
        $mahasiswa = User::factory()->has(Project::factory()->count(2))->create();
        $project = $mahasiswa->projects->first();

        $this->get('/')
            ->assertOk()
            ->assertSee($project->judul)
            ->assertSee($mahasiswa->program_studi)
            ->assertDontSee($mahasiswa->email)
            ->assertDontSee($project->api_key_secure);
    }

    public function test_portal_works_without_portfolio_owner(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Portofolio Unggulan');
    }
}
