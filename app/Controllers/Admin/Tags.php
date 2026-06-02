<?php

namespace App\Controllers\Admin;

use App\Models\TagModel;

class Tags extends BaseController
{
    public function index(): string
    {
        $tagModel = new TagModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Tag Management',
            'tags'      => $tagModel->getTagsWithCounts(),
            'user_name' => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/tags/index', $data)
             . view('admin/templates/footer');
    }

    public function create(): \CodeIgniter\HTTP\RedirectResponse
    {
        $tagModel = new TagModel();

        $rules = [
            'name_en' => 'required|max_length[100]',
            'name_hi' => 'required|max_length[100]',
            'slug'    => 'required|max_length[100]|is_unique[tags.slug]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tagModel->insert([
            'name_en' => $this->request->getPost('name_en'),
            'name_hi' => $this->request->getPost('name_hi'),
            'slug'    => url_title($this->request->getPost('slug'), '-', true),
        ]);

        return redirect()->to('/' . $this->locale . '/admin/tags')
                       ->with('message', 'Tag created successfully');
    }

    public function edit(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $tagModel = new TagModel();
        $tag = $tagModel->find($id);

        if (!$tag) {
            return redirect()->to('/' . $this->locale . '/admin/tags')
                           ->with('error', 'Tag not found');
        }

        $rules = [
            'name_en' => 'required|max_length[100]',
            'name_hi' => 'required|max_length[100]',
            'slug'    => "required|max_length[100]|is_unique[tags.slug,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tagModel->update($id, [
            'name_en' => $this->request->getPost('name_en'),
            'name_hi' => $this->request->getPost('name_hi'),
            'slug'    => url_title($this->request->getPost('slug'), '-', true),
        ]);

        return redirect()->to('/' . $this->locale . '/admin/tags')
                       ->with('message', 'Tag updated successfully');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $tagModel = new TagModel();
        $tagModel->delete($id);

        return redirect()->to('/' . $this->locale . '/admin/tags')
                       ->with('message', 'Tag deleted successfully');
    }
}
