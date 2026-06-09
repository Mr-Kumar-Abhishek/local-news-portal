<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index(): string
    {
        $categoryModel = new CategoryModel();

        $data = [
            'locale'     => $this->locale,
            'title'      => 'Category Management',
            'categories' => $categoryModel->getCategoriesWithCounts(),
            'user_name'  => $this->getCurrentUserName(),
        ];

        return view('admin/templates/header', $data)
             . view('admin/categories/index', $data)
             . view('admin/templates/footer');
    }

    public function create(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $categoryModel = new CategoryModel();

        $data = [
            'locale'        => $this->locale,
            'title'         => 'Create Category',
            'parent_categories' => $categoryModel->getParentCategories(),
            'user_name'     => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name_en' => 'required|max_length[100]',
                'name_hi' => 'required|max_length[100]',
                'slug'    => 'required|max_length[100]|is_unique[categories.slug]',
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                return view('admin/templates/header', $data)
                     . view('admin/categories/create', $data)
                     . view('admin/templates/footer');
            }

            $categoryId = $categoryModel->insert([
                'name_en'     => $this->request->getPost('name_en'),
                'name_hi'     => $this->request->getPost('name_hi'),
                'slug'        => url_title($this->request->getPost('slug'), '-', true),
                'description' => $this->request->getPost('description'),
                'parent_id'   => $this->request->getPost('parent_id') ?: null,
                'status'      => $this->request->getPost('status') ?? 1,
            ]);

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'category_created',
                'entity_type' => 'category',
                'entity_id' => $categoryId,
                'description' => "Category '{$this->request->getPost('name_en')}' created",
                'ip_address' => $this->request->getIPAddress(),
            ]);

            return redirect()->to('/' . $this->locale . '/admin/categories')
                           ->with('message', 'Category created successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/categories/create', $data)
             . view('admin/templates/footer');
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/' . $this->locale . '/admin/categories')
                           ->with('error', 'Category not found');
        }

        $data = [
            'locale'           => $this->locale,
            'title'            => 'Edit Category',
            'category'         => $category,
            'parent_categories' => $categoryModel->getParentCategories(),
            'user_name'        => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name_en' => 'required|max_length[100]',
                'name_hi' => 'required|max_length[100]',
                'slug'    => "required|max_length[100]|is_unique[categories.slug,id,{$id}]",
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                return view('admin/templates/header', $data)
                     . view('admin/categories/edit', $data)
                     . view('admin/templates/footer');
            }

            $categoryModel->update($id, [
                'name_en'     => $this->request->getPost('name_en'),
                'name_hi'     => $this->request->getPost('name_hi'),
                'slug'        => url_title($this->request->getPost('slug'), '-', true),
                'description' => $this->request->getPost('description'),
                'parent_id'   => $this->request->getPost('parent_id') ?: null,
                'status'      => $this->request->getPost('status') ?? 1,
            ]);

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'category_updated',
                'entity_type' => 'category',
                'entity_id' => $id,
                'description' => "Category '{$this->request->getPost('name_en')}' updated",
                'ip_address' => $this->request->getIPAddress(),
            ]);

            return redirect()->to('/' . $this->locale . '/admin/categories')
                           ->with('message', 'Category updated successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/categories/edit', $data)
             . view('admin/templates/footer');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);
        $categoryModel->delete($id);

        // Log activity
        if ($category) {
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'category_deleted',
                'entity_type' => 'category',
                'entity_id' => $id,
                'description' => "Category '{$category->name_en}' deleted",
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        return redirect()->to('/' . $this->locale . '/admin/categories')
                       ->with('message', 'Category deleted successfully');
    }
}
