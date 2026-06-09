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
                // SEO Settings
                'meta_title', 'seo_meta_description', 'seo_meta_keywords',
                'seo_google_analytics_id', 'og_image_url', 'twitter_handle',
            ];

            foreach ($allowedKeys as $key) {
                $value = $this->request->getPost($key);
                if ($value !== null) {
                    $settingModel->setSetting($key, $value);
                }
            }

            // Log activity
            $activityLog = new \App\Models\ActivityLogModel();
            $activityLog->log([
                'user_id' => $this->getCurrentUserId(),
                'action' => 'settings_saved',
                'entity_type' => 'setting',
                'entity_id' => null,
                'description' => 'Site settings saved',
                'ip_address' => $this->request->getIPAddress(),
            ]);

            return redirect()->to('/' . $this->locale . '/admin/settings')
                           ->with('message', 'Settings saved successfully');
        }

        return view('admin/templates/header', $data)
             . view('admin/settings/index', $data)
             . view('admin/templates/footer');
    }
}
