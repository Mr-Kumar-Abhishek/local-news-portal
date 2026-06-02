<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table            = 'media';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'filename', 'filepath', 'filetype', 'filesize',
        'alt_text_en', 'alt_text_hi', 'uploaded_by',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'filename' => 'required|max_length[255]',
        'filepath' => 'required|max_length[500]',
    ];

    protected $skipValidation = false;

    public function getRecentMedia(int $limit = 20): array
    {
        $builder = $this->db->table('media');
        $builder->select('media.*, users.full_name as uploader_name');
        $builder->join('users', 'users.id = media.uploaded_by', 'left');
        $builder->orderBy('media.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    public function getImages(string $type = 'image'): array
    {
        $builder = $this->db->table('media');
        $builder->select('media.*, users.full_name as uploader_name');
        $builder->join('users', 'users.id = media.uploaded_by', 'left');

        if ($type === 'image') {
            $builder->like('media.filetype', 'image');
        }

        $builder->orderBy('media.created_at', 'DESC');

        return $builder->get()->getResult();
    }

    public function getTotalSize(): int
    {
        return $this->selectSum('filesize')->get()->getRow()->filesize ?? 0;
    }

    public function getTotalFiles(): int
    {
        return $this->countAllResults();
    }
}
