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
}
