<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;
use Notification;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;


    public function setUp() :void
    {
        parent::setUp();

        // fake all notifications that are sent out during tests
        Notification::fake();

        // create a user
        User::factory()->create([
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword')
        ]);

    }

    public function test_non_authenticated_user_cannot_get_user_details()
    {

        $response = $this->json('GET', route('verification.resend'));

        $response->assertStatus(401)
            ->assertSee('Unauthenticated');
    }

    public function test_authenticated_user_can_get_user_details()
    {
        Sanctum::actingAs(
            User::first(),
        );

        $response = $this->json('GET', route('verification.resend'));

        $response->assertStatus(400);
    }
}