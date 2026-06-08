<?php

/**
 * Media Helper for Hind Bihar
 *
 * Provides functions for generating media URLs (thumbnails, medium versions,
 * placeholders) used throughout the application.
 *
 * Usage:
 *   echo media_url('uploads/2025/01/photo.jpg');
 *   echo thumbnail_url('uploads/2025/01/photo.jpg');
 *   echo placeholder_image();
 */

// -----------------------------------------------------------------------------
// Media URL
// -----------------------------------------------------------------------------

if (!function_exists('media_url')) {
    /**
     * Return the full base URL to a media file.
     *
     * @param string $path Relative path to the media file.
     * @return string Full URL.
     */
    function media_url(string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // If already a full URL, return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return base_url(ltrim($path, '/'));
    }
}

// -----------------------------------------------------------------------------
// Thumbnail URL
// -----------------------------------------------------------------------------

if (!function_exists('thumbnail_url')) {
    /**
     * Return the URL to the thumbnail version of a media file.
     *
     * Convention: original file "image.jpg" has thumbnail "image_thumb.jpg"
     * in the same directory.
     *
     * @param string $path Relative path to the original media file.
     * @return string Full URL to the thumbnail version.
     */
    function thumbnail_url(string $path): string
    {
        if (empty($path)) {
            return '';
        }

        $pathInfo = pathinfo($path);
        $dirname  = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $thumbPath = $dirname . $pathInfo['filename'] . '_thumb.' . ($pathInfo['extension'] ?? 'jpg');

        return media_url($thumbPath);
    }
}

// -----------------------------------------------------------------------------
// Medium URL
// -----------------------------------------------------------------------------

if (!function_exists('medium_url')) {
    /**
     * Return the URL to the medium-sized version of a media file.
     *
     * Convention: original file "image.jpg" has medium version "image_medium.jpg"
     * in the same directory.
     *
     * @param string $path Relative path to the original media file.
     * @return string Full URL to the medium version.
     */
    function medium_url(string $path): string
    {
        if (empty($path)) {
            return '';
        }

        $pathInfo   = pathinfo($path);
        $dirname    = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $mediumPath = $dirname . $pathInfo['filename'] . '_medium.' . ($pathInfo['extension'] ?? 'jpg');

        return media_url($mediumPath);
    }
}

// -----------------------------------------------------------------------------
// Placeholder Image
// -----------------------------------------------------------------------------

if (!function_exists('placeholder_image')) {
    /**
     * Return a placeholder image URL with the given dimensions.
     *
     * Uses a built-in placeholder or an external service as fallback.
     *
     * @param int $width  Width in pixels.
     * @param int $height Height in pixels.
     * @return string URL to placeholder image.
     */
    function placeholder_image(int $width = 800, int $height = 600): string
    {
        // Check if there's a local placeholder image
        $localPath = 'uploads/media/placeholder_' . $width . 'x' . $height . '.png';

        if (file_exists(FCPATH . $localPath)) {
            return base_url($localPath);
        }

        // Use a simple SVG data URI as fallback
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '"'
             . ' viewBox="0 0 ' . $width . ' ' . $height . '">'
             . '<rect width="100%" height="100%" fill="#e9ecef"/>'
             . '<text x="50%" y="50%" font-family="Arial, sans-serif" font-size="16"'
             . ' fill="#6c757d" text-anchor="middle" dominant-baseline="middle">'
             . $width . '×' . $height
             . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
