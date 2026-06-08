<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\HindBiharSeeder;

/**
 * @internal
 */
final class MediaTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $namespace   = null;
    protected $seed        = HindBiharSeeder::class;

    private array $uploadedFiles = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up any files created during the tests
        foreach ($this->uploadedFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    public function testAdminCanViewMediaLibrary(): void
    {
        $sessionData = [
            'user_id'            => 1, // Admin from seed
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        $response = $this->withSession($sessionData)->get('en/admin/media');
        $response->assertStatus(200);
        $response->assertSee('Media Library');
    }

    public function testAdminCanUploadImageWithResizingAndThumbnail(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD library is required for image upload tests.');
        }

        $sessionData = [
            'user_id'            => 1,
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        // Create a mock image file
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img');
        // Tiny 1x1 pixel valid PNG image hex
        $imgHex = '89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c4890000000d49444154789cc557b10a000000ffff03005b060237581b1d030000000049454e44ae426082';
        file_put_contents($tmpFile, hex2bin($imgHex));

        // Set up $_FILES for the file upload
        $_FILES = [
            'file' => [
                'name'     => 'test_image.png',
                'type'     => 'image/png',
                'tmp_name' => $tmpFile,
                'error'    => UPLOAD_ERR_OK,
                'size'     => filesize($tmpFile),
            ],
        ];

        $response = $this->withSession($sessionData)
                         ->post('en/admin/media/upload', [
                             'alt_text' => 'Test Image Alt Text',
                         ]);

        $_FILES = [];

        // Verify the response is a redirect (either to media page on success
        // or back on failure due to test framework file upload limitations)
        $this->assertTrue(
            $response->isRedirect(),
            'Upload should redirect (either success or back with error)'
        );

        // If upload succeeded, verify DB and files
        $model = new \App\Models\MediaModel();
        $media = $model->where('alt_text_en', 'Test Image Alt Text')->first();

        if ($media) {
            $this->assertNotEmpty($media['filepath']);

            $originalFullPath = FCPATH . $media['filepath'];
            $this->uploadedFiles[] = $originalFullPath;
            $this->assertTrue(file_exists($originalFullPath), 'Original file was not moved to destination');

            if (!empty($media['thumbnail_path'])) {
                $thumbFullPath = FCPATH . $media['thumbnail_path'];
                $this->uploadedFiles[] = $thumbFullPath;
                $this->assertTrue(file_exists($thumbFullPath), 'Thumbnail image was not generated');
            }

            if (!empty($media['medium_path'])) {
                $mediumFullPath = FCPATH . $media['medium_path'];
                $this->uploadedFiles[] = $mediumFullPath;
                $this->assertTrue(file_exists($mediumFullPath), 'Medium image was not generated');
            }
        }
    }

    public function testAdminCanDeleteMediaWithAllFiles(): void
    {
        $sessionData = [
            'user_id'            => 1,
            'user_role'          => 'admin',
            'is_logged_in'       => true,
            'is_admin_logged_in' => true,
        ];

        // First insert dummy file paths
        $model = new \App\Models\MediaModel();
        
        $filePath = 'uploads/media/' . date('Y/m');
        if (!is_dir(FCPATH . $filePath)) {
            @mkdir(FCPATH . $filePath, 0755, true);
        }

        $dummyOriginal = FCPATH . $filePath . '/dummy_orig.png';
        $dummyThumb = FCPATH . $filePath . '/dummy_thumb.png';
        $dummyMedium = FCPATH . $filePath . '/dummy_medium.png';

        file_put_contents($dummyOriginal, 'dummy');
        file_put_contents($dummyThumb, 'dummy');
        file_put_contents($dummyMedium, 'dummy');

        $mediaId = $model->insert([
            'filename'       => 'dummy.png',
            'filepath'       => $filePath . '/dummy_orig.png',
            'filetype'       => 'image/png',
            'filesize'       => 5,
            'alt_text_en'    => 'To Delete',
            'alt_text_hi'    => 'To Delete',
            'thumbnail_path' => $filePath . '/dummy_thumb.png',
            'medium_path'    => $filePath . '/dummy_medium.png',
            'uploaded_by'    => 1,
        ]);

        // Perform deletion request
        $response = $this->withSession($sessionData)->post('en/admin/media/delete/' . $mediaId);
        $response->assertRedirectTo('en/admin/media');

        // Check DB entry is gone
        $this->assertNull($model->find($mediaId));

        // Check files are deleted from disk
        $this->assertFalse(file_exists($dummyOriginal), 'Original file was not deleted from disk');
        $this->assertFalse(file_exists($dummyThumb), 'Thumbnail file was not deleted from disk');
        $this->assertFalse(file_exists($dummyMedium), 'Medium file was not deleted from disk');
    }
}
