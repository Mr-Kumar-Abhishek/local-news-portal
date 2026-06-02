<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'email', 'password', 'full_name',
        'role', 'language_preference', 'status',
        'remember_token', 'remember_expires_at',
        'reset_token', 'reset_expires_at',
        'verification_token', 'email_verified_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'full_name'=> 'permit_empty|max_length[100]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword(array $data): array
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function attemptLogin(string $email, string $password): ?object
    {
        $user = $this->where('email', $email)->where('status', 1)->first();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }

    public function getUsersWithCounts(): array
    {
        return $this->select('users.*, (SELECT COUNT(*) FROM articles WHERE articles.author_id = users.id) as article_count')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getTotalUsers(): int
    {
        return $this->where('status', 1)->countAllResults();
    }

    public function getRecentUsers(int $limit = 5): array
    {
        return $this->where('status', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
