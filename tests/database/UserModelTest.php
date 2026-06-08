<?php

namespace Tests\Database;

use Tests\Support\DatabaseTestCase;
use App\Models\UserModel;

/**
 * @internal
 */
final class UserModelTest extends DatabaseTestCase
{

    public function testUserCreationAndHashing(): void
    {
        $model = new UserModel();

        // Create a new reader user
        $userId = $model->insert([
            'username'            => 'testreader',
            'email'               => 'reader@example.com',
            'password'            => 'reader123',
            'full_name'           => 'Test Reader',
            'role'                => 'reader',
            'language_preference' => 'en',
            'status'              => 1,
        ]);

        $this->assertNotEmpty($userId);

        $user = $model->find($userId);
        $this->assertNotNull($user);
        $this->assertEquals('testreader', $user->username);
        $this->assertNotEquals('reader123', $user->password); // Must be hashed
        $this->assertTrue(password_verify('reader123', $user->password));
    }

    public function testAttemptLoginSuccess(): void
    {
        $model = new UserModel();

        // Seeder creates admin with admin123
        $user = $model->attemptLogin('admin@hindbihar.com', 'admin123');
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->username);
    }

    public function testAttemptLoginFailure(): void
    {
        $model = new UserModel();

        // Invalid password
        $user = $model->attemptLogin('admin@hindbihar.com', 'wrongpassword');
        $this->assertNull($user);

        // Invalid email
        $user = $model->attemptLogin('nonexistent@example.com', 'admin123');
        $this->assertNull($user);
    }

    public function testGetUsersWithCounts(): void
    {
        $model = new UserModel();
        $users = $model->getUsersWithCounts();
        
        $this->assertNotEmpty($users);
        $this->assertObjectHasProperty('article_count', $users[0]);
    }

    public function testTotalUsersAndRecentUsers(): void
    {
        $model = new UserModel();
        
        // Initially, we should have at least 1 user (admin)
        $total = $model->getTotalUsers();
        $this->assertGreaterThanOrEqual(1, $total);

        $recent = $model->getRecentUsers(5);
        $this->assertNotEmpty($recent);
    }
}
