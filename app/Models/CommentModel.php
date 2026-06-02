<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'article_id', 'user_id', 'author_name',
        'author_email', 'body', 'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'article_id'  => 'required|numeric',
        'author_name' => 'permit_empty|max_length[100]',
        'author_email'=> 'permit_empty|valid_email|max_length[100]',
        'body'        => 'required',
    ];

    protected $skipValidation = false;

    public function getApprovedComments(int $articleId): array
    {
        return $this->where('article_id', $articleId)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getAllWithDetails(): array
    {
        $builder = $this->db->table('comments');
        $builder->select('comments.*, articles.title_en as article_title, articles.slug as article_slug');
        $builder->join('articles', 'articles.id = comments.article_id', 'left');
        $builder->orderBy('comments.created_at', 'DESC');

        return $builder->get()->getResult();
    }

    public function getPendingCount(): int
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    public function getTotalComments(): int
    {
        return $this->where('status', 'approved')->countAllResults();
    }

    public function getRecentComments(int $limit = 5): array
    {
        $builder = $this->db->table('comments');
        $builder->select('comments.*, articles.title_en as article_title, articles.slug as article_slug');
        $builder->join('articles', 'articles.id = comments.article_id', 'left');
        $builder->where('comments.status', 'approved');
        $builder->orderBy('comments.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }
}
