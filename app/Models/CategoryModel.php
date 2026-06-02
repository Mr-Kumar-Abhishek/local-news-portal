<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name_en', 'name_hi', 'slug', 'description',
        'parent_id', 'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name_en' => 'required|max_length[100]',
        'name_hi' => 'required|max_length[100]',
        'slug'    => 'required|max_length[100]|is_unique[categories.slug,id,{id}]',
    ];

    protected $skipValidation = false;

    public function getActiveCategories(): array
    {
        return $this->where('status', 1)
                    ->orderBy('name_en', 'ASC')
                    ->findAll();
    }

    public function getParentCategories(): array
    {
        return $this->where('parent_id', null)
                    ->where('status', 1)
                    ->orderBy('name_en', 'ASC')
                    ->findAll();
    }

    public function getChildCategories(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
                    ->where('status', 1)
                    ->orderBy('name_en', 'ASC')
                    ->findAll();
    }

    public function getCategoriesWithCounts(): array
    {
        return $this->select('categories.*, (SELECT COUNT(*) FROM articles WHERE articles.category_id = categories.id AND articles.status = "published") as article_count')
                    ->orderBy('categories.name_en', 'ASC')
                    ->findAll();
    }

    public function getCategoryTree(): array
    {
        $categories = $this->where('status', 1)->findAll();
        $tree = [];

        foreach ($categories as $cat) {
            if ($cat->parent_id === null) {
                $cat->children = [];
                $tree[$cat->id] = $cat;
            }
        }

        foreach ($categories as $cat) {
            if ($cat->parent_id !== null && isset($tree[$cat->parent_id])) {
                $tree[$cat->parent_id]->children[] = $cat;
            }
        }

        return $tree;
    }
}
