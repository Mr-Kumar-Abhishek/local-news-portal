<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['key', 'value'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'key' => 'required|max_length[100]|is_unique[settings.key,id,{id}]',
    ];

    protected $skipValidation = false;

    public function getSetting(string $key): ?string
    {
        $setting = $this->where('key', $key)->first();
        return $setting ? $setting->value : null;
    }

    public function setSetting(string $key, string $value): bool
    {
        $existing = $this->where('key', $key)->first();

        if ($existing) {
            return $this->update($existing->id, ['value' => $value]);
        }

        return $this->insert(['key' => $key, 'value' => $value]) !== false;
    }

    public function getAllSettings(): array
    {
        $settings = $this->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }
}
