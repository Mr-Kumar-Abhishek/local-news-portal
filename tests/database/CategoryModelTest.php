<?php

namespace Tests\Database;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\CategoryModel;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class CategoryModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $namespace = null;
    protected $seed      = HindBiharSeeder::class;

    public function testGetActiveCategories(): void
    {
        $model = new CategoryModel();
        $categories = $model->getActiveCategories();
        
        $this->assertNotEmpty($categories);
        $this->assertCount(8, $categories); // Seeder creates 8 categories
    }

    public function testParentAndChildHierarchy(): void
    {
        $model = new CategoryModel();

        // Find Politics category created by seeder
        $politics = $model->where('slug', 'politics')->first();
        $this->assertNotNull($politics);

        // Add a child category to politics
        $childId = $model->insert([
            'name_en'   => 'Elections',
            'name_hi'   => 'चुनाव',
            'slug'      => 'elections-news',
            'parent_id' => $politics->id,
            'status'    => 1,
        ]);
        $this->assertNotEmpty($childId);

        // Get parents
        $parents = $model->getParentCategories();
        foreach ($parents as $parent) {
            $this->assertNull($parent->parent_id);
        }

        // Get children of politics
        $children = $model->getChildCategories($politics->id);
        $this->assertCount(1, $children);
        $this->assertEquals('Elections', $children[0]->name_en);
    }

    public function testCategoryTree(): void
    {
        $model = new CategoryModel();
        
        // Setup tree structure
        $politics = $model->where('slug', 'politics')->first();
        $model->insert([
            'name_en'   => 'State Politics',
            'name_hi'   => 'राज्य राजनीति',
            'slug'      => 'state-politics',
            'parent_id' => $politics->id,
            'status'    => 1,
        ]);

        $tree = $model->getCategoryTree();
        $this->assertNotEmpty($tree);
        $this->assertArrayHasKey($politics->id, $tree);
        $this->assertNotEmpty($tree[$politics->id]->children);
        $this->assertEquals('State Politics', $tree[$politics->id]->children[0]->name_en);
    }

    public function testCategoriesWithCounts(): void
    {
        $model = new CategoryModel();
        $categories = $model->getCategoriesWithCounts();
        $this->assertNotEmpty($categories);
        $this->assertObjectHasProperty('article_count', $categories[0]);
    }
}
