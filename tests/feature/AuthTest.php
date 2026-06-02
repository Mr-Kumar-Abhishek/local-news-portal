<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $namespace = null;
    protected $seed      = HindBiharSeeder::class;

    public function testAdminAccessWithoutLoginRedirectsToLogin(): void
    {
        $response = $this->get('en/admin/dashboard');

        // We expect it to redirect to the correct login route /en/login
        $response->assertRedirectTo('en/login');
    }

    public function testUserRegistrationRedirectsToLogin(): void
    {
        $response = $this->post('en/register', [
            'username'  => 'newuser',
            'email'     => 'newuser@example.com',
            'password'  => 'newuser123',
            'full_name' => 'New User',
        ]);

        // Registration should redirect to /en/login
        $response->assertRedirectTo('en/login');
    }

    public function testLoginSuccess(): void
    {
        $response = $this->post('en/login', [
            'email'    => 'admin@hindbihar.com',
            'password' => 'admin123',
        ]);

        // Login should redirect to admin dashboard
        $response->assertRedirectTo('en/admin/dashboard');
    }

    public function testLoginWithRememberMeSetsCookie(): void
    {
        $response = $this->post('en/login', [
            'email'    => 'admin@hindbihar.com',
            'password' => 'admin123',
            'remember' => '1',
        ]);

        $response->assertRedirectTo('en/admin/dashboard');

        // Verify cookie is set on the response
        $cookies = $response->response()->getCookies();
        $this->assertNotEmpty($cookies);
        $found = false;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'remember_me') {
                $found = true;
                $value = $cookie->getValue();
                $this->assertStringStartsWith('1:', $value); // Admin has user_id = 1
                break;
            }
        }
        $this->assertTrue($found, 'remember_me cookie not found in response');
    }

    public function testAutoLoginWithValidRememberMeCookie(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = 'somerandomtokenstring1234567890abc';
        $hashedToken = hash('sha256', $token);
        
        $model->update($user->id, [
            'remember_token'      => $hashedToken,
            'remember_expires_at' => \CodeIgniter\I18n\Time::now()->addHours(1)->toDateTimeString(),
        ]);

        // Set the cookie in global and Superglobals service
        $_COOKIE['remember_me'] = $user->id . ':' . $token;
        service('superglobals')->setGlobalArray('cookie', ['remember_me' => $user->id . ':' . $token]);

        // Perform GET request to homepage (or any route where remember me filter runs)
        // Since remember me filter will run globally, it should log user in.
        $response = $this->get('en/');
        
        // Check if session contains user_id after request
        $this->assertEquals($user->id, session()->get('user_id'));
        $this->assertTrue(session()->get('is_logged_in'));

        // Clean up
        unset($_COOKIE['remember_me']);
        service('superglobals')->setGlobalArray('cookie', []);
    }

    public function testAutoLoginWithExpiredRememberMeCookie(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = 'somerandomtokenstring1234567890abc';
        $hashedToken = hash('sha256', $token);
        
        $model->update($user->id, [
            'remember_token'      => $hashedToken,
            'remember_expires_at' => \CodeIgniter\I18n\Time::now()->subHours(1)->toDateTimeString(),
        ]);

        $_COOKIE['remember_me'] = $user->id . ':' . $token;
        service('superglobals')->setGlobalArray('cookie', ['remember_me' => $user->id . ':' . $token]);

        $response = $this->get('en/');
        
        $this->assertNull(session()->get('user_id'));
        $this->assertNotTrue(session()->get('is_logged_in'));

        unset($_COOKIE['remember_me']);
        service('superglobals')->setGlobalArray('cookie', []);
    }

    public function testLogoutClearsRememberMeCookieAndDbToken(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = 'somerandomtokenstring1234567890abc';
        $hashedToken = hash('sha256', $token);
        
        $model->update($user->id, [
            'remember_token'      => $hashedToken,
            'remember_expires_at' => \CodeIgniter\I18n\Time::now()->addHours(1)->toDateTimeString(),
        ]);

        $_COOKIE['remember_me'] = $user->id . ':' . $token;
        service('superglobals')->setGlobalArray('cookie', ['remember_me' => $user->id . ':' . $token]);

        // Set up the session as logged in
        $sessionData = [
            'user_id'      => $user->id,
            'is_logged_in' => true,
        ];

        $response = $this->withSession($sessionData)->get('en/logout');

        // DB tokens should be cleared
        $updatedUser = $model->find($user->id);
        $this->assertNull($updatedUser->remember_token);
        $this->assertNull($updatedUser->remember_expires_at);

        // Cookie should be deleted/expired in the response
        $cookies = $response->response()->getCookies();
        $found = false;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'remember_me') {
                $found = true;
                $this->assertTrue($cookie->isExpired() || empty($cookie->getValue()));
                break;
            }
        }
        unset($_COOKIE['remember_me']);
        service('superglobals')->setGlobalArray('cookie', []);
    }

    public function testForgotPasswordViewRenders(): void
    {
        $response = $this->get('en/forgot-password');
        $response->assertStatus(200);
        $response->assertSee(lang('News.forgot_password_title'));
        $this->assertStringContainsString('name="email"', $response->response()->getBody());
    }

    public function testSendResetLinkInvalidEmail(): void
    {
        $response = $this->post('en/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);
        $response->assertSee(lang('News.email_not_found'));
    }

    public function testSendResetLinkSuccess(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        
        // Ensure reset fields are empty before test
        $model->update($user->id, [
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);

        $response = $this->post('en/forgot-password', [
            'email' => 'admin@hindbihar.com',
        ]);

        $response->assertSee(lang('News.reset_link_sent'));

        $updatedUser = $model->find($user->id);
        $this->assertNotEmpty($updatedUser->reset_token);
        $this->assertNotNull($updatedUser->reset_expires_at);
        $this->assertGreaterThan(time(), strtotime($updatedUser->reset_expires_at));
    }

    public function testResetPasswordViewWithInvalidToken(): void
    {
        $response = $this->get('en/reset-password/someinvalidtoken');
        $response->assertSee(lang('News.invalid_or_expired_token'));
    }

    public function testResetPasswordViewWithValidToken(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        $model->update($user->id, [
            'reset_token'      => $hashedToken,
            'reset_expires_at' => \CodeIgniter\I18n\Time::now()->addHours(1)->toDateTimeString(),
        ]);

        $response = $this->get('en/reset-password/' . $token);
        $response->assertStatus(200);
        $response->assertSee(lang('News.reset_password_title'));
        $this->assertStringContainsString('name="password"', $response->response()->getBody());
        $this->assertStringContainsString('name="password_confirm"', $response->response()->getBody());
    }

    public function testAttemptResetSuccess(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        $model->update($user->id, [
            'reset_token'      => $hashedToken,
            'reset_expires_at' => \CodeIgniter\I18n\Time::now()->addHours(1)->toDateTimeString(),
        ]);

        $response = $this->post('en/reset-password/' . $token, [
            'password'         => 'newsecurepassword123',
            'password_confirm' => 'newsecurepassword123',
        ]);

        $response->assertRedirectTo('en/login');

        // Check password changed
        $updatedUser = $model->find($user->id);
        $this->assertTrue(password_verify('newsecurepassword123', $updatedUser->password));
        $this->assertNull($updatedUser->reset_token);
        $this->assertNull($updatedUser->reset_expires_at);
    }

    public function testAttemptResetWithExpiredToken(): void
    {
        $model = new \App\Models\UserModel();
        $user = $model->where('email', 'admin@hindbihar.com')->first();
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        $model->update($user->id, [
            'reset_token'      => $hashedToken,
            'reset_expires_at' => \CodeIgniter\I18n\Time::now()->subSeconds(60)->toDateTimeString(),
        ]);

        $response = $this->post('en/reset-password/' . $token, [
            'password'         => 'newsecurepassword123',
            'password_confirm' => 'newsecurepassword123',
        ]);

        $response->assertSee(lang('News.invalid_or_expired_token'));
    }
}
