<?php

namespace App\Controllers\Admin;

use App\Models\SettingModel;

class Settings extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $settingModel = new SettingModel();

        $data = [
            'locale'    => $this->locale,
            'title'     => 'Site Settings',
            'settings'  => $settingModel->getAllSettings(),
            'user_name' => $this->getCurrentUserName(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $allowedKeys = [
                'site_name_en', 'site_name_hi', 'site_description_en', 'site_description_hi',
                'site_keywords', 'site_logo', 'site_favicon', 'facebook_url', 'twitter_url',
                'instagram_url', 'youtube_url', 'footer_text_en', 'footer_text_hi',
                'posts_per_page', 'maintenance_mode',
            ];

            foreach ($allowedKeys as $key) {
                $value = $this->request->getPost($key);
                if ($value !== null) {
                    $settingModel->setSetting($key, $value);
                }
            }

            return redirect()->to('/' . $this->locale . '/admin/settings')
                           ->with('message', 'Settings saved successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/settings/index', $data)
             . view('admin/templates/footer');
    }
}
