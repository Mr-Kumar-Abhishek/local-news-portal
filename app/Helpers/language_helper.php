<?php

/**
 * Language Helper for Hind Bihar
 *
 * Provides functions for language switching, detection, and metadata
 * in the bilingual (English/Hindi) news website.
 *
 * Usage:
 *   echo lang_switch_url('hi');
 *   $current = current_lang();
 *   echo lang_name('hi');
 */

// -----------------------------------------------------------------------------
// Language Switch URL
// -----------------------------------------------------------------------------

if (!function_exists('lang_switch_url')) {
    /**
     * Generate a URL for switching to the given language while preserving
     * the current path structure.
     *
     * Example: If current URL is /en/news/some-slug, lang_switch_url('hi')
     * returns /hi/news/some-slug.
     *
     * @param string $lang Target language code ('en' or 'hi').
     * @return string Full URL with the target language prefix.
     */
    function lang_switch_url(string $lang): string
    {
        if (!in_array($lang, ['en', 'hi'], true)) {
            $lang = 'en';
        }

        $uri      = service('request')->getUri();
        $segments = $uri->getSegments();

        // Replace or prepend the locale segment
        if (!empty($segments) && in_array($segments[0], ['en', 'hi'], true)) {
            $segments[0] = $lang;
        } else {
            array_unshift($segments, $lang);
        }

        return base_url(implode('/', $segments));
    }
}

// -----------------------------------------------------------------------------
// Current Language
// -----------------------------------------------------------------------------

if (!function_exists('current_lang')) {
    /**
     * Return the current language code from session or default to 'en'.
     *
     * @return string Language code ('en' or 'hi').
     */
    function current_lang(): string
    {
        $session = session();
        $lang    = $session->get('lang');

        if ($lang && in_array($lang, ['en', 'hi'], true)) {
            return $lang;
        }

        // Try to detect from URL
        $uri      = service('request')->getUri();
        $segments = $uri->getSegments();

        if (!empty($segments) && in_array($segments[0], ['en', 'hi'], true)) {
            return $segments[0];
        }

        return 'en';
    }
}

// -----------------------------------------------------------------------------
// Language Name
// -----------------------------------------------------------------------------

if (!function_exists('lang_name')) {
    /**
     * Return the human-readable name for a language code.
     *
     * @param string $code Language code ('en' or 'hi').
     * @return string Human-readable language name.
     */
    function lang_name(string $code): string
    {
        $names = [
            'en' => 'English',
            'hi' => 'हिन्दी',
        ];

        return $names[$code] ?? 'Unknown';
    }
}

// -----------------------------------------------------------------------------
// Is RTL
// -----------------------------------------------------------------------------

if (!function_exists('is_rtl')) {
    /**
     * Determine whether the current language is right-to-left.
     * Hindi (Devanagari) is LTR, but this allows future RTL support.
     *
     * @return bool True if the current language is RTL.
     */
    function is_rtl(): bool
    {
        $rtlLanguages = ['ar', 'ur', 'fa', 'he', 'ps', 'sd'];

        return in_array(current_lang(), $rtlLanguages, true);
    }
}
