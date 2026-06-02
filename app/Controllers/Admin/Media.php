<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;

class Media extends BaseController
{
    public function index(): string
    {
        $mediaModel = new MediaModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Media Library',
            'media'     => $mediaModel->getRecentMedia(50),
            'user_name' => $this->getCurrentUserName(),
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

        $mediaModel = new MediaModel();
        $mediaModel->insert([
            'filename'    => $file->getName(),
            'filepath'    => $filePath . '/' . $newName,
            'filetype'    => $file->getMimeType(),
            'filesize'    => $file->getSize(),
            'alt_text_en' => $this->request->getPost('alt_text_en'),
            'alt_text_hi' => $this->request->getPost('alt_text_hi'),
            'uploaded_by' => $this->getCurrentUserId(),
        ]);

        return redirect()->to('/' . $this->locale . '/admin/media')
                       ->with('message', 'File uploaded successfully');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $mediaModel = new MediaModel();
        $media = $mediaModel->find($id);

        if ($media) {
            // Delete physical file
            $fullPath = FCPATH . $media->filepath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $mediaModel->delete($id);
        }

        return redirect()->to('/' . $this->locale . '/admin/media')
                       ->with('message', 'File deleted successfully');
    }
}
