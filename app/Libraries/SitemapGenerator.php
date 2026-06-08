<?php

namespace App\Libraries;

/**
 * Sitemap XML Generator for Hind Bihar
 *
 * Generates standards-compliant XML sitemaps for search engine submission.
 * Supports URL entries with optional lastmod, changefreq, and priority.
 *
 * Usage:
 *   $sitemap = new SitemapGenerator();
 *   $sitemap->addUrl('https://example.com/en/news/slug', '2025-01-15', 'daily', 0.8);
 *   $xml = $sitemap->generate();
 *   $sitemap->saveToFile(FCPATH . 'sitemap.xml');
 */
class SitemapGenerator
{
    /**
     * Collected URL entries.
     *
     * @var array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: float}>
     */
    protected array $urls = [];

    /**
     * XML declaration and opening tag.
     */
    protected string $xmlOpen = '<?xml version="1.0" encoding="UTF-8"?>';

    /**
     * Sitemap namespace.
     */
    protected string $namespace = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * Valid changefreq values.
     */
    protected array $validChangefreq = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

    // -------------------------------------------------------------------------
    // Add URL
    // -------------------------------------------------------------------------

    /**
     * Add a URL entry to the sitemap.
     *
     * @param string      $loc        Full URL of the page (required).
     * @param string|null $lastmod    Last modification date in YYYY-MM-DD or W3C format.
     * @param string      $changefreq Expected change frequency (default 'daily').
     * @param float       $priority   Priority between 0.0 and 1.0 (default 0.5).
     */
    public function addUrl(
        string $loc,
        ?string $lastmod = null,
        string $changefreq = 'daily',
        float $priority = 0.5
    ): void {
        // Validate changefreq
        if (!in_array($changefreq, $this->validChangefreq, true)) {
            $changefreq = 'daily';
        }

        // Clamp priority to 0.0–1.0 range
        $priority = max(0.0, min(1.0, $priority));

        // Round to one decimal place
        $priority = round($priority, 1);

        $this->urls[] = [
            'loc'        => $loc,
            'lastmod'    => $lastmod,
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

    // -------------------------------------------------------------------------
    // Generate
    // -------------------------------------------------------------------------

    /**
     * Generate the complete XML sitemap string.
     *
     * @return string The XML sitemap as a string.
     */
    public function generate(): string
    {
        $xml  = $this->xmlOpen . "\n";
        $xml .= '<urlset xmlns="' . $this->namespace . '">' . "\n";

        foreach ($this->urls as $url) {
            $xml .= $this->buildUrlEntry($url);
        }

        $xml .= '</urlset>' . "\n";

        return $xml;
    }

    // -------------------------------------------------------------------------
    // Save to File
    // -------------------------------------------------------------------------

    /**
     * Generate the sitemap XML and save it to a file.
     *
     * @param string $path Full filesystem path where the sitemap should be saved.
     * @return bool True on success, false on failure.
     */
    public function saveToFile(string $path): bool
    {
        $xml = $this->generate();

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $result = file_put_contents($path, $xml, LOCK_EX);

        return $result !== false;
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    /**
     * Get the number of URLs currently stored.
     *
     * @return int Number of URL entries.
     */
    public function getUrlCount(): int
    {
        return count($this->urls);
    }

    /**
     * Get all stored URLs.
     *
     * @return array<int, array> The URL entries array.
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * Clear all stored URLs.
     */
    public function clear(): void
    {
        $this->urls = [];
    }

    // -------------------------------------------------------------------------
    // Protected Helpers
    // -------------------------------------------------------------------------

    /**
     * Build a single <url> XML entry.
     *
     * @param array $url URL data with 'loc', 'lastmod', 'changefreq', 'priority'.
     * @return string XML string for the <url> element.
     */
    protected function buildUrlEntry(array $url): string
    {
        $entry  = '  <url>' . "\n";
        $entry .= '    <loc>' . esc($url['loc']) . '</loc>' . "\n";

        if (!empty($url['lastmod'])) {
            $entry .= '    <lastmod>' . esc($url['lastmod']) . '</lastmod>' . "\n";
        }

        $entry .= '    <changefreq>' . esc($url['changefreq']) . '</changefreq>' . "\n";
        $entry .= '    <priority>' . number_format($url['priority'], 1) . '</priority>' . "\n";
        $entry .= '  </url>' . "\n";

        return $entry;
    }
}
