<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;

class Media extends BaseController
{
    public function index(): string
    {
        $mediaModel = new MediaModel();

        $data = [
            'locale'      => $this->locale,
            'title'       => 'Media Library',
            'media_files' => $mediaModel->getRecentMedia(50),
            'user_name'   => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/media/index', $data)
             . view('admin/templates/footer');
    }

    public function upload(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Invalid file upload');
        }

        $newName = $file->getRandomName();
        $filePath = 'uploads/media/' . date('Y/m');

        if (!is_dir(FCPATH . $filePath)) {
            mkdir(FCPATH . $filePath, 0755, true);
        }

        $file->move(FCPATH . $filePath, $newName);

        $mimeType = $file->getMimeType();
        $thumbnailPath = null;
        $mediumPath = null;

        // Perform resizing & thumbnail generation for images
        if (strpos($mimeType, 'image/') === 0) {
            $imageService = \Config\Services::image();
            $originalFullPath = FCPATH . $filePath . '/' . $newName;

            // Generate medium image (resize to fit 1200x1200px)
            $mediumFullPath = FCPATH . $filePath . '/medium_' . $newName;
            try {
                $imageService->withFile($originalFullPath)
                             ->resize(1200, 1200, true, 'auto')
                             ->save($mediumFullPath);
                $mediumPath = $filePath . '/medium_' . $newName;
            } catch (\Exception $e) {
                log_message('error', 'Failed to create medium image: ' . $e->getMessage());
            }

            // Generate thumbnail (fit/crop to 150x150px)
            $thumbFullPath = FCPATH . $filePath . '/thumb_' . $newName;
            try {
                $imageService->withFile($originalFullPath)
                             ->fit(150, 150, 'center')
                             ->save($thumbFullPath);
                $thumbnailPath = $filePath . '/thumb_' . $newName;
            } catch (\Exception $e) {
                log_message('error', 'Failed to create thumbnail: ' . $e->getMessage());
            }
        }

        $mediaModel = new MediaModel();
        $mediaModel->insert([
            'filename'       => $file->getName(),
            'filepath'       => $filePath . '/' . $newName,
            'filetype'       => $mimeType,
            'filesize'       => $file->getSize(),
            'alt_text_en'    => $this->request->getPost('alt_text'),
            'alt_text_hi'    => $this->request->getPost('alt_text'),
            'thumbnail_path' => $thumbnailPath,
            'medium_path'    => $mediumPath,
            'uploaded_by'    => $this->getCurrentUserId(),
        ]);

        return redirect()->to('/' . $this->locale . '/admin/media')
                       ->with('message', 'File uploaded successfully');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $mediaModel = new MediaModel();
        $media = $mediaModel->find($id);

        if ($media) {
            // Delete physical files
            $filesToDelete = [
                FCPATH . $media['filepath'],
                !empty($media['thumbnail_path']) ? FCPATH . $media['thumbnail_path'] : null,
                !empty($media['medium_path']) ? FCPATH . $media['medium_path'] : null,
            ];
            foreach ($filesToDelete as $file) {
                if ($file && file_exists($file)) {
                    unlink($file);
                }
            }

            $mediaModel->delete($id);
        }

        return redirect()->to('/' . $this->locale . '/admin/media')
                       ->with('message', 'File deleted successfully');
    }
}
