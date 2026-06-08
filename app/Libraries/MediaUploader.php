<?php

namespace App\Libraries;

use CodeIgniter\Files\File;
use Config\Services;
use RuntimeException;

/**
 * Media Uploader Library for Hind Bihar
 *
 * Handles media file uploads with automatic thumbnail and medium-size
 * generation using the GD image library.
 *
 * Usage:
 *   $uploader = new MediaUploader();
 *   $result = $uploader->upload($file);
 */
class MediaUploader
{
    /**
     * Base upload path.
     */
    protected string $uploadPath;

    /**
     * Allowed MIME types.
     */
    protected array $allowedTypes;

    /**
     * Maximum file size in kilobytes.
     */
    protected int $maxSize;

    /**
     * Thumbnail dimensions.
     */
    protected int $thumbWidth  = 150;
    protected int $thumbHeight = 150;

    /**
     * Medium image dimensions (max width/height, aspect ratio preserved).
     */
    protected int $mediumWidth  = 1200;
    protected int $mediumHeight = 1200;

    /**
     * JPEG quality (0-100).
     */
    protected int $jpegQuality = 85;

    /**
     * WebP quality (0-100).
     */
    protected int $webpQuality = 85;

    /**
     * Constructor.
     *
     * @param array $config Optional configuration overrides.
     */
    public function __construct(array $config = [])
    {
        $this->uploadPath   = $config['uploadPath'] ?? ROOTPATH . 'public/uploads/media/';
        $this->allowedTypes = $config['allowedTypes'] ?? ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $this->maxSize      = $config['maxSize'] ?? 5120; // 5MB in KB

        if (!empty($config['thumbWidth'])) {
            $this->thumbWidth = (int) $config['thumbWidth'];
        }
        if (!empty($config['thumbHeight'])) {
            $this->thumbHeight = (int) $config['thumbHeight'];
        }
        if (!empty($config['mediumWidth'])) {
            $this->mediumWidth = (int) $config['mediumWidth'];
        }
        if (!empty($config['mediumHeight'])) {
            $this->mediumHeight = (int) $config['mediumHeight'];
        }
        if (isset($config['jpegQuality'])) {
            $this->jpegQuality = (int) $config['jpegQuality'];
        }
    }

    // -------------------------------------------------------------------------
    // Upload
    // -------------------------------------------------------------------------

    /**
     * Upload a file, validate it, and generate thumbnail/medium versions.
     *
     * @param File $file The uploaded file instance.
     * @return array Associative array with 'success' (bool), 'filename', 'paths',
     *               or 'error' on failure.
     */
    public function upload(File $file): array
    {
        // ----- Validate MIME type -----
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $this->allowedTypes, true)) {
            return [
                'success' => false,
                'error'   => 'File type "' . $mimeType . '" is not allowed. Allowed types: '
                           . implode(', ', $this->allowedTypes),
            ];
        }

        // ----- Validate file size (KB) -----
        $fileSizeKB = (int) round($file->getSize() / 1024);

        if ($fileSizeKB > $this->maxSize) {
            return [
                'success' => false,
                'error'   => 'File size (' . $fileSizeKB . ' KB) exceeds maximum allowed size ('
                           . $this->maxSize . ' KB).',
            ];
        }

        // ----- Generate unique filename -----
        $originalName = $file->getFilename();
        $extension    = $this->getExtension($originalName, $mimeType);
        $uniqueName   = $this->generateUniqueName($extension);

        // ----- Create dated subdirectory (Y/m) -----
        $datedDir = date('Y') . '/' . date('m');
        $fullDir  = rtrim($this->uploadPath, '/') . '/' . $datedDir;

        if (!is_dir($fullDir)) {
            if (!mkdir($fullDir, 0755, true)) {
                return [
                    'success' => false,
                    'error'   => 'Failed to create upload directory: ' . $fullDir,
                ];
            }
        }

        // ----- Move uploaded file -----
        $destPath = $fullDir . '/' . $uniqueName;

        try {
            $file->move($fullDir, $uniqueName);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => 'Failed to move uploaded file: ' . $e->getMessage(),
            ];
        }

        // ----- Generate thumbnail and medium versions -----
        $thumbPath  = '';
        $mediumPath = '';

        if ($this->isSupportedImage($extension)) {
            $thumbPath  = $this->createResized($destPath, $fullDir, $uniqueName, $extension, $this->thumbWidth, $this->thumbHeight, '_thumb');
            $mediumPath = $this->createResized($destPath, $fullDir, $uniqueName, $extension, $this->mediumWidth, $this->mediumHeight, '_medium');
        }

        // Build relative paths
        $relativeBase = $datedDir . '/' . $uniqueName;
        $relThumb     = $thumbPath ? $datedDir . '/' . basename($thumbPath) : '';
        $relMedium    = $mediumPath ? $datedDir . '/' . basename($mediumPath) : '';

        return [
            'success'      => true,
            'filename'     => $uniqueName,
            'original'     => $relativeBase,
            'thumbnail'    => $relThumb,
            'medium'       => $relMedium,
            'mime_type'    => $mimeType,
            'size_kb'      => $fileSizeKB,
            'directory'    => $datedDir,
        ];
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    /**
     * Delete an uploaded file and its thumbnail/medium versions.
     *
     * @param string $filePath Relative file path (e.g., "2025/01/photo.jpg").
     * @return bool True on success, false on failure.
     */
    public function delete(string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        $fullPath = rtrim($this->uploadPath, '/') . '/' . ltrim($filePath, '/');
        $success  = true;

        // Delete original
        if (file_exists($fullPath)) {
            if (!unlink($fullPath)) {
                $success = false;
            }
        }

        // Delete thumbnail and medium versions
        $pathInfo = pathinfo($fullPath);
        $dirname  = $pathInfo['dirname'];
        $stem     = $pathInfo['filename'];
        $ext      = $pathInfo['extension'] ?? '';

        $thumbFile  = $dirname . '/' . $stem . '_thumb.' . $ext;
        $mediumFile = $dirname . '/' . $stem . '_medium.' . $ext;

        if (file_exists($thumbFile)) {
            unlink($thumbFile);
        }
        if (file_exists($mediumFile)) {
            unlink($mediumFile);
        }

        return $success;
    }

    // -------------------------------------------------------------------------
    // Setter / Getter
    // -------------------------------------------------------------------------

    /**
     * Set the upload path.
     */
    public function setUploadPath(string $path): self
    {
        $this->uploadPath = rtrim($path, '/') . '/';
        return $this;
    }

    /**
     * Get the upload path.
     */
    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }

    /**
     * Set allowed MIME types.
     */
    public function setAllowedTypes(array $types): self
    {
        $this->allowedTypes = $types;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Protected Helpers
    // -------------------------------------------------------------------------

    /**
     * Create a resized version of an image using GD.
     *
     * @param string $sourcePath  Full path to source image.
     * @param string $destDir     Destination directory.
     * @param string $baseName    Base filename without suffix.
     * @param string $extension   File extension.
     * @param int    $maxWidth    Maximum width.
     * @param int    $maxHeight   Maximum height.
     * @param string $suffix      Filename suffix (e.g., '_thumb', '_medium').
     * @return string Full path to resized image, or empty string on failure.
     */
    protected function createResized(
        string $sourcePath,
        string $destDir,
        string $baseName,
        string $extension,
        int $maxWidth,
        int $maxHeight,
        string $suffix
    ): string {
        if (!extension_loaded('gd')) {
            return '';
        }

        // Load source image
        $sourceImage = $this->imageCreateFromFile($sourcePath, $extension);

        if (!$sourceImage) {
            return '';
        }

        $srcWidth  = imagesx($sourceImage);
        $srcHeight = imagesy($sourceImage);

        // Calculate new dimensions preserving aspect ratio
        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1.0);

        $newWidth  = (int) round($srcWidth * $ratio);
        $newHeight = (int) round($srcHeight * $ratio);

        // Create destination image
        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF/WebP
        $this->preserveAlpha($destImage, $extension);

        // Resample
        imagecopyresampled(
            $destImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $srcWidth,
            $srcHeight
        );

        // Build output filename
        $pathInfo   = pathinfo($baseName);
        $destName   = $pathInfo['filename'] . $suffix . '.' . $extension;
        $destPath   = $destDir . '/' . $destName;

        // Save
        $saved = $this->imageSaveToFile($destImage, $destPath, $extension);

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destImage);

        return $saved ? $destPath : '';
    }

    /**
     * Create a GD image resource from a file.
     *
     * @param string $path      Full path to the image file.
     * @param string $extension File extension.
     * @return \GdImage|resource|false GD image resource or false on failure.
     */
    protected function imageCreateFromFile(string $path, string $extension)
    {
        if (!file_exists($path)) {
            return false;
        }

        switch (strtolower($extension)) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($path);

            case 'png':
                return imagecreatefrompng($path);

            case 'gif':
                return imagecreatefromgif($path);

            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    return imagecreatefromwebp($path);
                }
                return false;

            default:
                return false;
        }
    }

    /**
     * Save a GD image resource to a file.
     *
     * @param \GdImage|resource $image     GD image resource.
     * @param string            $destPath  Destination file path.
     * @param string            $extension File extension.
     * @return bool True on success, false on failure.
     */
    protected function imageSaveToFile($image, string $destPath, string $extension): bool
    {
        switch (strtolower($extension)) {
            case 'jpeg':
            case 'jpg':
                return imagejpeg($image, $destPath, $this->jpegQuality);

            case 'png':
                // PNG quality: 0 (no compression) to 9
                $pngQuality = (int) round(9 - ($this->jpegQuality / 100) * 9);
                return imagepng($image, $destPath, $pngQuality);

            case 'gif':
                return imagegif($image, $destPath);

            case 'webp':
                if (function_exists('imagewebp')) {
                    return imagewebp($image, $destPath, $this->webpQuality);
                }
                return false;

            default:
                return false;
        }
    }

    /**
     * Preserve alpha transparency for PNG, GIF, and WebP images.
     *
     * @param \GdImage|resource $image     GD image resource.
     * @param string            $extension File extension.
     */
    protected function preserveAlpha($image, string $extension): void
    {
        $ext = strtolower($extension);

        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        } elseif ($ext === 'gif') {
            // For GIF, set transparent color
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
            imagecolortransparent($image, $transparent);
        }
    }

    /**
     * Determine file extension from filename and MIME type.
     */
    protected function getExtension(string $filename, string $mimeType): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!empty($ext) && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return $ext;
        }

        // Fallback: map MIME type to extension
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];

        return $map[$mimeType] ?? 'jpg';
    }

    /**
     * Generate a unique filename with the given extension.
     */
    protected function generateUniqueName(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;
    }

    /**
     * Check if the file extension is a supported image format for resizing.
     */
    protected function isSupportedImage(string $extension): bool
    {
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }
}
