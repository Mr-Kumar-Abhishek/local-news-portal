<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title_en', 'title_hi', 'content_en', 'content_hi', 'slug',
        'excerpt_en', 'excerpt_hi', 'featured_image', 'category_id',
        'author_id', 'language', 'news_section', 'status',
        'published_at', 'featured', 'view_count',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title_en'     => 'required|max_length[500]',
        'title_hi'     => 'required|max_length[500]',
        'slug'         => 'required|max_length[500]|is_unique[articles.slug,id,{id}]',
    ];

    protected $skipValidation = false;

    // Relationships
    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(UserModel::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(TagModel::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function getPublished(array $filters = [], int $perPage = 10, int $offset = 0): array
    {
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name_en as category_name, categories.name_hi as category_name_hi, categories.slug as category_slug, users.full_name as author_name');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->join('users', 'users.id = articles.author_id', 'left');
        $builder->where('articles.status', 'published');

        if (!empty($filters['category_id'])) {
            $builder->where('articles.category_id', $filters['category_id']);
        }

        if (!empty($filters['news_section'])) {
            $builder->where('articles.news_section', $filters['news_section']);
        }

        if (!empty($filters['language'])) {
            $builder->where('articles.language', $filters['language']);
        }

        if (!empty($filters['featured'])) {
            $builder->where('articles.featured', 1);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart();
            $builder->like('articles.title_en', $search);
            $builder->orLike('articles.title_hi', $search);
            $builder->orLike('articles.content_en', $search);
            $builder->orLike('articles.content_hi', $search);
            $builder->groupEnd();
        }

        if (!empty($filters['author_id'])) {
            $builder->where('articles.author_id', $filters['author_id']);
        }

        $builder->orderBy('articles.published_at', 'DESC');
        $builder->limit($perPage, $offset);

        return $builder->get()->getResult();
    }

    public function countPublished(array $filters = []): int
    {
        $builder = $this->db->table('articles');
        $builder->where('articles.status', 'published');

        if (!empty($filters['category_id'])) {
            $builder->where('articles.category_id', $filters['category_id']);
        }

        if (!empty($filters['news_section'])) {
            $builder->where('articles.news_section', $filters['news_section']);
        }

        if (!empty($filters['language'])) {
            $builder->where('articles.language', $filters['language']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart();
            $builder->like('articles.title_en', $search);
            $builder->orLike('articles.title_hi', $search);
            $builder->orLike('articles.content_en', $search);
            $builder->orLike('articles.content_hi', $search);
            $builder->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function getArticleBySlug(string $slug): ?object
    {
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name_en as category_name, categories.name_hi as category_name_hi, categories.slug as category_slug, users.full_name as author_name');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->join('users', 'users.id = articles.author_id', 'left');
        $builder->where('articles.slug', $slug);
        $builder->where('articles.status', 'published');

        return $builder->get()->getRow();
    }

    public function getArticleTags(int $articleId): array
    {
        $builder = $this->db->table('article_tags');
        $builder->select('tags.*');
        $builder->join('tags', 'tags.id = article_tags.tag_id');
        $builder->where('article_tags.article_id', $articleId);

        return $builder->get()->getResult();
    }

    public function getFeaturedArticles(int $limit = 5): array
    {
        return $this->getPublished(['featured' => 1], $limit);
    }

    public function getLatestArticles(int $limit = 10): array
    {
        return $this->getPublished([], $limit);
    }

    public function getArticlesBySection(string $section, int $limit = 10): array
    {
        return $this->getPublished(['news_section' => $section], $limit);
    }

    public function getArticlesByCategory(int $categoryId, int $limit = 10): array
    {
        return $this->getPublished(['category_id' => $categoryId], $limit);
    }

    public function incrementViewCount(int $articleId): void
    {
        $this->db->table('articles')
                 ->set('view_count', 'view_count + 1', false)
                 ->where('id', $articleId)
                 ->update();
    }

    public function getPopularArticles(int $limit = 5): array
    {
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name_en as category_name, categories.name_hi as category_name_hi, categories.slug as category_slug');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->where('articles.status', 'published');
        $builder->orderBy('articles.view_count', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    public function getRelatedArticles(int $articleId, int $categoryId, int $limit = 4): array
    {
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name_en as category_name, categories.name_hi as category_name_hi, categories.slug as category_slug');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->where('articles.status', 'published');
        $builder->where('articles.category_id', $categoryId);
        $builder->where('articles.id !=', $articleId);
        $builder->orderBy('articles.published_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    public function getTotalPublished(): int
    {
        return $this->where('status', 'published')->countAllResults();
    }

    public function getTotalViews(): int
    {
        return $this->selectSum('view_count')->where('status', 'published')->get()->getRow()->view_count ?? 0;
    }

    public function getAllWithDetails(): array
    {
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name_en as category_name, categories.name_hi as category_name_hi, users.full_name as author_name');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->join('users', 'users.id = articles.author_id', 'left');
        $builder->orderBy('articles.created_at', 'DESC');

        return $builder->get()->getResult();
    }

    public function searchArticles(string $query, int $limit = 20): array
    {
        return $this->getPublished(['search' => $query], $limit);
    }

    public function getMonthlyArticleCounts(): array
    {
        $builder = $this->db->table('articles');
        $builder->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count");
        $builder->where('status', 'published');
        $builder->groupBy("DATE_FORMAT(created_at, '%Y-%m')");
        $builder->orderBy('month', 'DESC');
        $builder->limit(12);

        return $builder->get()->getResult();
    }
}
