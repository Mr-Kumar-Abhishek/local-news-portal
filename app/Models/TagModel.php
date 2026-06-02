<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name_en', 'name_hi', 'slug'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name_en' => 'required|max_length[100]',
        'name_hi' => 'required|max_length[100]',
        'slug'    => 'required|max_length[100]|is_unique[tags.slug,id,{id}]',
    ];

    protected $skipValidation = false;

    public function getTagsWithCounts(): array
    {
        $builder = $this->db->table('tags');
        $builder->select('tags.*, (SELECT COUNT(*) FROM article_tags WHERE article_tags.tag_id = tags.id) as article_count');
        $builder->orderBy('tags.name_en', 'ASC');

        return $builder->get()->getResult();
    }

    public function getPopularTags(int $limit = 10): array
    {
        $builder = $this->db->table('tags');
        $builder->select('tags.*, (SELECT COUNT(*) FROM article_tags WHERE article_tags.tag_id = tags.id) as article_count');
        $builder->orderBy('article_count', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    public function syncTags(int $articleId, array $tagIds): void
    {
        // Remove existing associations
        $this->db->table('article_tags')->where('article_id', $articleId)->delete();

        // Insert new associations
        if (!empty($tagIds)) {
            $data = [];
            foreach ($tagIds as $tagId) {
                $data[] = [
                    'article_id' => $articleId,
                    'tag_id'     => $tagId,
                ];
            }
            $this->db->table('article_tags')->insertBatch($data);
        }
    }
}
