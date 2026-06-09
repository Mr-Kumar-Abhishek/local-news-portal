<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'action', 'entity_type', 'entity_id', 'description', 'ip_address',
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $skipValidation = true;

    /**
     * Insert a log entry.
     */
    public function log(array $data): bool
    {
        return (bool) $this->insert($data);
    }

    /**
     * Get recent log entries with user join.
     */
    public function getRecent(int $limit = 50): array
    {
        $builder = $this->db->table('activity_logs');
        $builder->select('activity_logs.*, users.full_name as user_name, users.username');
        $builder->join('users', 'users.id = activity_logs.user_id', 'left');
        $builder->orderBy('activity_logs.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    /**
     * Get logs for a specific user.
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        $builder = $this->db->table('activity_logs');
        $builder->select('activity_logs.*, users.full_name as user_name, users.username');
        $builder->join('users', 'users.id = activity_logs.user_id', 'left');
        $builder->where('activity_logs.user_id', $userId);
        $builder->orderBy('activity_logs.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    /**
     * Get logs for a specific entity.
     */
    public function getByEntity(string $type, int $id): array
    {
        $builder = $this->db->table('activity_logs');
        $builder->select('activity_logs.*, users.full_name as user_name, users.username');
        $builder->join('users', 'users.id = activity_logs.user_id', 'left');
        $builder->where('activity_logs.entity_type', $type);
        $builder->where('activity_logs.entity_id', $id);
        $builder->orderBy('activity_logs.created_at', 'DESC');

        return $builder->get()->getResult();
    }
}
