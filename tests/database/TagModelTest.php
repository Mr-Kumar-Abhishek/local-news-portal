<?php

namespace Tests\Database;

use App\Models\TagModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\HindBiharSeeder; // Assuming a seeder if needed, or we can use Fabricator

class TagModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = 'App\Database\Seeds\HindBiharSeeder';
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected TagModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TagModel();
    }

    public function testCanCreateTag()
    {
        $data = [
            'name_en' => 'Test Tag',
            'name_hi' => 'टेस्ट टैग',
            'slug'    => 'test-tag'
        ];

        $id = $this->model->insert($data);
        
        $this->assertIsNumeric($id);
        
        $tag = $this->model->find($id);
        $this->assertEquals('Test Tag', $tag->name_en);
        $this->assertEquals('टेस्ट टैग', $tag->name_hi);
        $this->assertEquals('test-tag', $tag->slug);
    }

    public function testValidationFailsWithoutRequiredFields()
    {
        $data = [
            'name_en' => 'Test Tag'
            // Missing name_hi and slug
        ];

        $result = $this->model->insert($data);
        
        $this->assertFalse($result);
        $errors = $this->model->errors();
        
        $this->assertArrayHasKey('name_hi', $errors);
        $this->assertArrayHasKey('slug', $errors);
    }

    public function testSlugMustBeUnique()
    {
        // First tag
        $this->model->insert([
            'name_en' => 'Technology',
            'name_hi' => 'प्रौद्योगिकी',
            'slug'    => 'technology'
        ]);

        // Second tag with same slug
        $result = $this->model->insert([
            'name_en' => 'Tech',
            'name_hi' => 'टेक',
            'slug'    => 'technology'
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('slug', $this->model->errors());
    }

    public function testGetPopularTags()
    {
        // Since we are running the HindBiharSeeder, we might have some initial tags.
        // Let's create some dummy article_tags manually if needed, or just test if method returns array
        $tags = $this->model->getPopularTags(5);
        
        $this->assertIsArray($tags);
        if (count($tags) > 0) {
            $this->assertObjectHasProperty('article_count', $tags[0]);
        }
    }
}
