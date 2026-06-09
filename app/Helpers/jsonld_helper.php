<?php

/**
 * JSON-LD Structured Data Helper for Hind Bihar
 *
 * Provides functions for generating JSON-LD structured data blocks
 * (Schema.org) for improved SEO and rich search results.
 *
 * Usage:
 *   echo article_jsonld($article);
 *   echo breadcrumb_jsonld($breadcrumbs);
 *   echo organization_jsonld();
 *   echo website_jsonld();
 */

use Config\Services;

// -----------------------------------------------------------------------------
// JSON Encode Helper
// -----------------------------------------------------------------------------

if (!function_exists('jsonld_encode')) {
    /**
     * Encode data as JSON-LD with proper escaping and formatting.
     *
     * @param mixed $data  Data to encode.
     * @return string JSON string suitable for <script type="application/ld+json">.
     */
    function jsonld_encode($data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
}

// -----------------------------------------------------------------------------
// JSON-LD Script Tag Wrapper
// -----------------------------------------------------------------------------

if (!function_exists('jsonld_script')) {
    /**
     * Wrap JSON string in a <script type="application/ld+json"> tag.
     *
     * @param string $json The JSON-LD string.
     * @return string HTML script tag.
     */
    function jsonld_script(string $json): string
    {
        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }
}

// -----------------------------------------------------------------------------
// Article JSON-LD (NewsArticle)
// -----------------------------------------------------------------------------

if (!function_exists('article_jsonld')) {
    /**
     * Generate NewsArticle JSON-LD for a single article.
     *
     * @param object $article The article object (with author_name, category_name, etc.).
     * @param string $locale  Current locale ('en' or 'hi').
     * @return string HTML script tag with JSON-LD.
     */
    function article_jsonld(object $article, string $locale = 'en'): string
    {
        $baseUrl = rtrim(base_url(), '/');
        $title   = $locale === 'hi' ? ($article->title_hi ?? '') : ($article->title_en ?? '');
        $desc    = $locale === 'hi' ? ($article->excerpt_hi ?? '') : ($article->excerpt_en ?? '');
        $content = $locale === 'hi' ? ($article->content_hi ?? '') : ($article->content_en ?? '');

        // Strip HTML from description
        $desc = trim(strip_tags((string) $desc));
        if (empty($desc)) {
            $desc = mb_substr(trim(strip_tags((string) $content)), 0, 160);
        }

        $image = $article->featured_image
            ? $baseUrl . '/' . ltrim($article->featured_image, '/')
            : null;

        $url = $baseUrl . '/' . $locale . '/news/' . ($article->slug ?? '');

        $authorName = $article->author_name ?? 'Hind Bihar';

        $published = $article->published_at ?? $article->created_at ?? date('c');
        $modified  = $article->updated_at ?? $published;

        $data = [
            '@context'      => 'https://schema.org',
            '@type'         => 'NewsArticle',
            'headline'      => $title,
            'description'   => $desc,
            'datePublished' => date('c', strtotime($published)),
            'dateModified'  => date('c', strtotime($modified)),
            'author'        => [
                '@type' => 'Person',
                'name'  => $authorName,
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => 'Hind Bihar',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $baseUrl . '/favicon.ico',
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => $url,
            ],
        ];

        if ($image) {
            $data['image'] = $image;
        }

        if (!empty($article->category_name)) {
            $data['articleSection'] = $locale === 'hi'
                ? ($article->category_name_hi ?? $article->category_name)
                : $article->category_name;
        }

        return jsonld_script(jsonld_encode($data));
    }
}

// -----------------------------------------------------------------------------
// Breadcrumb JSON-LD (BreadcrumbList)
// -----------------------------------------------------------------------------

if (!function_exists('breadcrumb_jsonld')) {
    /**
     * Generate BreadcrumbList JSON-LD from breadcrumb items.
     *
     * @param array  $items   Array of ['label' => string, 'url' => string|null] entries.
     * @param string $locale  Current locale ('en' or 'hi').
     * @return string HTML script tag with JSON-LD.
     */
    function breadcrumb_jsonld(array $items, string $locale = 'en'): string
    {
        if (empty($items)) {
            return '';
        }

        $baseUrl    = rtrim(base_url(), '/');
        $listItems  = [];
        $position   = 1;

        foreach ($items as $item) {
            $entry = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $item['label'] ?? '',
            ];

            if (!empty($item['url'])) {
                // Ensure absolute URL
                $url = $item['url'];
                if (strpos($url, 'http') !== 0) {
                    $url = $baseUrl . '/' . ltrim($url, '/');
                }
                $entry['item'] = $url;
            }

            $listItems[] = $entry;
            $position++;
        }

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];

        return jsonld_script(jsonld_encode($data));
    }
}

// -----------------------------------------------------------------------------
// Organization JSON-LD
// -----------------------------------------------------------------------------

if (!function_exists('organization_jsonld')) {
    /**
     * Generate Organization JSON-LD for Hind Bihar.
     *
     * @return string HTML script tag with JSON-LD.
     */
    function organization_jsonld(): string
    {
        $baseUrl = rtrim(base_url(), '/');

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => 'Hind Bihar',
            'url'      => $baseUrl,
            'logo'     => [
                '@type' => 'ImageObject',
                'url'   => $baseUrl . '/favicon.ico',
            ],
            'sameAs'   => [],
        ];

        // Add social profiles if available from settings
        try {
            $settingModel = model('App\Models\SettingModel');
            if ($fb = $settingModel->getSetting('social_facebook_url')) {
                $data['sameAs'][] = $fb;
            }
            if ($tw = $settingModel->getSetting('social_twitter_url')) {
                $data['sameAs'][] = $tw;
            }
            if ($yt = $settingModel->getSetting('social_youtube_url')) {
                $data['sameAs'][] = $yt;
            }
            if ($ig = $settingModel->getSetting('social_instagram_url')) {
                $data['sameAs'][] = $ig;
            }
        } catch (\Throwable $e) {
            // Settings may not be available; silently skip.
        }

        if (empty($data['sameAs'])) {
            unset($data['sameAs']);
        }

        return jsonld_script(jsonld_encode($data));
    }
}

// -----------------------------------------------------------------------------
// WebSite JSON-LD
// -----------------------------------------------------------------------------

if (!function_exists('website_jsonld')) {
    /**
     * Generate WebSite JSON-LD with SearchAction for Hind Bihar.
     *
     * @return string HTML script tag with JSON-LD.
     */
    function website_jsonld(): string
    {
        $baseUrl = rtrim(base_url(), '/');

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => 'Hind Bihar',
            'url'             => $baseUrl,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl . '/search?q={search_term_string}',
                ],
                'query-input' => [
                    '@type'           => 'PropertyValueSpecification',
                    'valueRequired'   => true,
                    'valueMaxlength'  => 100,
                    'valueName'       => 'search_term_string',
                ],
            ],
        ];

        return jsonld_script(jsonld_encode($data));
    }
}

// -----------------------------------------------------------------------------
// SearchAction JSON-LD
// -----------------------------------------------------------------------------

if (!function_exists('searchaction_jsonld')) {
    /**
     * Generate standalone SearchAction JSON-LD for search pages.
     *
     * @return string HTML script tag with JSON-LD.
     */
    function searchaction_jsonld(): string
    {
        $baseUrl = rtrim(base_url(), '/');

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => 'Hind Bihar',
            'url'             => $baseUrl,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $baseUrl . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return jsonld_script(jsonld_encode($data));
    }
}

// -----------------------------------------------------------------------------
// ItemList JSON-LD (for listing pages)
// -----------------------------------------------------------------------------

if (!function_exists('itemlist_jsonld')) {
    /**
     * Generate ItemList JSON-LD for a collection of articles (listing pages).
     *
     * @param array  $articles Array of article objects.
     * @param string $locale   Current locale ('en' or 'hi').
     * @param string $listName Optional name for the list.
     * @return string HTML script tag with JSON-LD.
     */
    function itemlist_jsonld(array $articles, string $locale = 'en', string $listName = 'Articles'): string
    {
        if (empty($articles)) {
            return '';
        }

        $baseUrl   = rtrim(base_url(), '/');
        $listItems = [];
        $position  = 1;

        foreach ($articles as $article) {
            $title = $locale === 'hi' ? ($article->title_hi ?? '') : ($article->title_en ?? '');
            $desc  = $locale === 'hi' ? ($article->excerpt_hi ?? '') : ($article->excerpt_en ?? '');
            $desc  = trim(strip_tags((string) $desc));

            $url = $baseUrl . '/' . $locale . '/news/' . ($article->slug ?? '');

            $listItems[] = [
                '@type'       => 'ListItem',
                'position'    => $position,
                'url'         => $url,
                'name'        => $title,
                'description' => $desc ?: null,
            ];

            $position++;
        }

        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'name'            => $listName,
            'itemListElement' => $listItems,
        ];

        return jsonld_script(jsonld_encode($data));
    }
}
