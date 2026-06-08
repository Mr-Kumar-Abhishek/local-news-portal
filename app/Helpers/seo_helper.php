<?php

/**
 * SEO Helper for Hind Bihar
 *
 * Provides functions for generating SEO meta tags, Open Graph tags,
 * and Twitter Card tags for improved social sharing and search visibility.
 *
 * Usage:
 *   echo meta_title('About Us');
 *   echo og_tags(['title' => 'News', 'image' => '/img/og.jpg']);
 */

use Config\Services;
use Config\App;

// -----------------------------------------------------------------------------
// Meta Title
// -----------------------------------------------------------------------------

if (!function_exists('meta_title')) {
    /**
     * Generate a page title with the site name suffix.
     *
     * @param string $title The page-specific title.
     * @return string Full title string, e.g. "About Us - Hind Bihar".
     */
    function meta_title(string $title): string
    {
        $siteName = setting('App.siteName') ?? 'Hind Bihar';
        return esc($title) . ' - ' . esc($siteName);
    }
}

// -----------------------------------------------------------------------------
// Meta Description
// -----------------------------------------------------------------------------

if (!function_exists('meta_description')) {
    /**
     * Truncate a description to the given maximum length for SEO meta tags.
     *
     * @param string $description The raw description text.
     * @param int    $maxLength   Maximum character count (default 160).
     * @return string Truncated description with ellipsis if needed.
     */
    function meta_description(string $description, int $maxLength = 160): string
    {
        // Strip HTML tags and decode entities
        $text = strip_tags($description);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (mb_strlen($text) <= $maxLength) {
            return esc($text);
        }

        // Truncate at last complete word within limit
        $truncated = mb_substr($text, 0, $maxLength);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return esc($truncated) . '…';
    }
}

// -----------------------------------------------------------------------------
// Open Graph Tags
// -----------------------------------------------------------------------------

if (!function_exists('og_tags')) {
    /**
     * Generate Open Graph meta tags for Facebook, LinkedIn, etc.
     *
     * @param array $data Associative array with keys: title, description,
     *                    image, url, type, site_name.
     * @return string HTML string of <meta property="og:..."> tags.
     */
    function og_tags(array $data): string
    {
        $defaults = [
            'title'       => setting('App.siteName') ?? 'Hind Bihar',
            'description' => '',
            'image'       => '',
            'url'         => current_url(),
            'type'        => 'article',
            'site_name'   => setting('App.siteName') ?? 'Hind Bihar',
        ];

        $data = array_merge($defaults, $data);
        $html = '';

        $properties = [
            'title'       => 'og:title',
            'description' => 'og:description',
            'image'       => 'og:image',
            'url'         => 'og:url',
            'type'        => 'og:type',
            'site_name'   => 'og:site_name',
        ];

        foreach ($properties as $key => $property) {
            if (!empty($data[$key])) {
                $html .= '<meta property="' . $property . '" content="' . esc($data[$key]) . '">' . "\n";
            }
        }

        // Add image dimensions if available
        if (!empty($data['image_width'])) {
            $html .= '<meta property="og:image:width" content="' . (int) $data['image_width'] . '">' . "\n";
        }
        if (!empty($data['image_height'])) {
            $html .= '<meta property="og:image:height" content="' . (int) $data['image_height'] . '">' . "\n";
        }

        return $html;
    }
}

// -----------------------------------------------------------------------------
// Twitter Card Tags
// -----------------------------------------------------------------------------

if (!function_exists('twitter_card')) {
    /**
     * Generate Twitter Card meta tags.
     *
     * @param array $data Associative array with keys: card, title, description,
     *                    image, site, creator.
     * @return string HTML string of <meta name="twitter:..."> tags.
     */
    function twitter_card(array $data): string
    {
        $defaults = [
            'card'        => 'summary_large_image',
            'title'       => setting('App.siteName') ?? 'Hind Bihar',
            'description' => '',
            'image'       => '',
            'site'        => '',
            'creator'     => '',
        ];

        $data = array_merge($defaults, $data);
        $html = '';

        $properties = [
            'card'        => 'twitter:card',
            'title'       => 'twitter:title',
            'description' => 'twitter:description',
            'image'       => 'twitter:image',
            'site'        => 'twitter:site',
            'creator'     => 'twitter:creator',
        ];

        foreach ($properties as $key => $name) {
            if (!empty($data[$key])) {
                $html .= '<meta name="' . $name . '" content="' . esc($data[$key]) . '">' . "\n";
            }
        }

        return $html;
    }
}
