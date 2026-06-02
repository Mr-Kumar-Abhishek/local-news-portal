<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class NewsPagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $namespace = null;
    protected $seed      = HindBiharSeeder::class;

    public function testHomepageLoadsSuccessfully(): void
    {
        $response = $this->get('en');
        $response->assertStatus(200);
        $response->assertSee('Hind Bihar');
    }

    public function testNewsListingPageLoadsSuccessfully(): void
    {
        $response = $this->get('en/news');
        $response->assertStatus(200);
    }

    public function testSearchPageLoadsSuccessfully(): void
    {
        $response = $this->get('en/search?q=politics');
        $response->assertStatus(200);
        $response->assertSee('Search Results');
    }

    public function testSearchAutocompleteApiReturnsJson(): void
    {
        $response = $this->get('en/search/autocomplete?q=politics');
        $response->assertStatus(200);
        $response->assertHeaderPresent('Content-Type');
    }
}
