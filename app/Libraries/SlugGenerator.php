<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Slug Generator Library for Hind Bihar
 *
 * Generates URL-safe slugs from text strings, with special handling for
 * Hindi/Devanagari transliteration and uniqueness enforcement.
 *
 * Usage:
 *   $slug = SlugGenerator::generate('बिहार समाचार');  // "bihaar-samaachaar"
 *   $slug = SlugGenerator::unique('my-slug', ArticleModel::class);
 */
class SlugGenerator
{
    /**
     * Generate a URL-safe slug from arbitrary text.
     *
     * - Transliterates non-ASCII characters (including Hindi/Devanagari)
     *   to their closest ASCII/Latin equivalents.
     * - Lowercases the text.
     * - Replaces non-alphanumeric characters with the divider.
     * - Removes duplicate dividers.
     * - Trims dividers from both ends.
     *
     * @param string $text    The input text to slugify.
     * @param string $divider The word divider character (default '-').
     * @return string The generated slug.
     */
    public static function generate(string $text, string $divider = '-'): string
    {
        if (empty($text)) {
            return '';
        }

        // Transliterate non-ASCII characters (Devanagari, etc.)
        $text = self::transliterate($text);

        // Lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Replace non-alphanumeric characters (except the divider) with divider
        $text = preg_replace('/[^a-z0-9' . preg_quote($divider, '/') . ']+/u', $divider, $text);

        // Replace multiple consecutive dividers with a single one
        $text = preg_replace('/' . preg_quote($divider, '/') . '{2,}/', $divider, $text);

        // Remove dividers at the beginning and end
        $text = trim($text, $divider);

        return $text ?: 'untitled';
    }

    /**
     * Ensure slug uniqueness by checking against a database model/field.
     *
     * If the slug already exists, appends "-2", "-3", etc. until a unique
     * value is found.
     *
     * @param string      $slug       The desired slug.
     * @param string      $modelClass Fully qualified model class name.
     * @param string      $field      The database field to check (default 'slug').
     * @param int|null    $excludeId  Optional record ID to exclude from uniqueness check
     *                                (useful when updating an existing record).
     * @return string A unique slug.
     */
    public static function unique(
        string $slug,
        string $modelClass,
        string $field = 'slug',
        ?int $excludeId = null
    ): string {
        if (!class_exists($modelClass)) {
            return $slug;
        }

        $model  = new $modelClass();
        $db     = Database::connect();
        $table  = $model->table;

        $originalSlug = $slug;
        $counter      = 1;

        while (true) {
            $builder = $db->table($table);
            $builder->where($field, $slug);

            if ($excludeId !== null) {
                $primaryKey = $model->primaryKey ?? 'id';
                $builder->where($primaryKey . ' !=', $excludeId);
            }

            $exists = $builder->countAllResults();

            if ($exists === 0) {
                break;
            }

            $counter++;
            $slug = $originalSlug . '-' . $counter;

            // Safety limit to prevent infinite loops
            if ($counter > 100) {
                $slug = $originalSlug . '-' . bin2hex(random_bytes(4));
                break;
            }
        }

        return $slug;
    }

    // -------------------------------------------------------------------------
    // Transliteration
    // -------------------------------------------------------------------------

    /**
     * Transliterate non-ASCII text to ASCII/Latin equivalents.
     *
     * Provides a comprehensive mapping for Hindi/Devanagari characters
     * to their phonetic Latin representations.
     *
     * @param string $text The text to transliterate.
     * @return string ASCII-safe transliterated text.
     */
    protected static function transliterate(string $text): string
    {
        // If text is already ASCII, return as-is
        if (mb_check_encoding($text, 'ASCII')) {
            return $text;
        }

        // Devanagari (Hindi) transliteration map
        static $devanagariMap = [
            // Vowels
            'अ' => 'a',   'आ' => 'aa',  'इ' => 'i',   'ई' => 'ee',
            'उ' => 'u',   'ऊ' => 'oo',  'ऋ' => 'ri',  'ए' => 'e',
            'ऐ' => 'ai',  'ओ' => 'o',   'औ' => 'au',

            // Vowel signs (matras)
            'ा' => 'aa',  'ि' => 'i',   'ी' => 'ee',  'ु' => 'u',
            'ू' => 'oo',  'ृ' => 'ri',  'े' => 'e',   'ै' => 'ai',
            'ो' => 'o',   'ौ' => 'au',

            // Consonants - Velar
            'क' => 'k',   'ख' => 'kh',  'ग' => 'g',   'घ' => 'gh',  'ङ' => 'ng',

            // Consonants - Palatal
            'च' => 'ch',  'छ' => 'chh', 'ज' => 'j',   'झ' => 'jh',  'ञ' => 'ny',

            // Consonants - Retroflex
            'ट' => 't',   'ठ' => 'th',  'ड' => 'd',   'ढ' => 'dh',  'ण' => 'n',

            // Consonants - Dental
            'त' => 't',   'थ' => 'th',  'द' => 'd',   'ध' => 'dh',  'न' => 'n',

            // Consonants - Labial
            'प' => 'p',   'फ' => 'ph',  'ब' => 'b',   'भ' => 'bh',  'म' => 'm',

            // Consonants - Semi-vowels
            'य' => 'y',   'र' => 'r',   'ल' => 'l',   'व' => 'v',

            // Consonants - Sibilants
            'श' => 'sh',  'ष' => 'sh',  'स' => 's',   'ह' => 'h',

            // Special
            'क्ष' => 'ksh', 'त्र' => 'tr',  'ज्ञ' => 'gy',
            'श्र' => 'shr',

            // Nuqta variants
            'क़' => 'q',  'ख़' => 'kh', 'ग़' => 'gh', 'ज़' => 'z',
            'फ़' => 'f',  'ड़' => 'r',  'ढ़' => 'rh',

            // Punctuation / marks
            'ं' => 'n',   'ः' => 'h',   'ँ' => 'n',
            '।' => '',    '॥' => '',

            // Digits
            '०' => '0',   '१' => '1',   '२' => '2',   '३' => '3',   '४' => '4',
            '५' => '5',   '६' => '6',   '७' => '7',   '८' => '8',   '९' => '9',
        ];

        // Perform character-by-character replacement
        $output = '';
        $length = mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');

            if (isset($devanagariMap[$char])) {
                $output .= $devanagariMap[$char];
            } else {
                // Check if it is non-English letter and try iconv fallback
                if (ord($char) > 127 && function_exists('transliterator_transliterate')) {
                    $trans = transliterator_transliterate('Any-Latin; Latin-ASCII', $char);
                    $output .= ($trans !== false) ? $trans : $char;
                } else {
                    $output .= $char;
                }
            }
        }

        return $output;
    }
}
