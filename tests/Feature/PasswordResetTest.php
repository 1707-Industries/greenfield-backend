<?php

namespace Tests\Feature;

use App\Notifications\ResetPasswordNotification;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use function GuzzleHttp\Psr7\build_query;

class PasswordResetTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class)->create([
            'email' => 'user@example.com'
        ]);

        Notification::fake();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserReceivesAPasswordResetEmail()
    {
        $user = User::where('email', 'user@example.com')->first();

        // Make the request
        $response = $this->post('/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(201)->assertJson([
            'status' => 'We have e-mailed your password reset link!',
        ]);

        // Check to see if an email was sent to user@example.com
        Notification::assertSentTo($user, ResetPasswordNotification::class, function($notification) use($user){
            $mailData = $notification->toMail($user)->toArray();
            $this->assertEquals(sprintf('%s/reset-password/?%s', url(config('app.frontend_url')), build_query([
                'token' => $notification->token,
                'email' => $user->email,
            ])), $mailData['actionUrl']);
            return true;
        });
    }

    public function testUserCanResetTheirPassword()
    {
        $user = User::where('email', 'user@example.com')->first();

        // Make the request
        $this->post('/auth/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function($notification) use($user){
            $response = $this->post('/auth/reset-password', [
                'email' => $user->email,
                'token' => $notification->token,
                'password' => 'Password123'
            ]);

            $response->assertStatus(200);

            $user->refresh();

            $this->assertTrue(Hash::check('Password123', $user->password));
            return true;
        });
    }
}
