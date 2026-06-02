<?php

/**
 * Breadcrumb Helper for Hind Bihar
 *
 * Renders Bootstrap 5 breadcrumb navigation.
 *
 * Usage:
 *   echo breadcrumb([
 *       ['label' => 'Home', 'url' => '/en'],
 *       ['label' => 'News', 'url' => null],  // last item: no link
 *   ]);
 */

if (!function_exists('breadcrumb')) {
    /**
     * Render a Bootstrap 5 breadcrumb navigation.
     *
     * @param array $items Array of ['label' => string, 'url' => string|null] entries.
     *                     The last item should have 'url' => null (rendered as active text).
     * @return string HTML for the breadcrumb <li> items.
     */
    function breadcrumb(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html    = '';
        $lastIdx = count($items) - 1;

        foreach ($items as $idx => $item) {
            $label = esc($item['label'] ?? '');
            $url   = $item['url'] ?? null;
            $isLast = ($idx === $lastIdx);

            if ($isLast || $url === null) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $label . '</a></li>';
            }
        }

        return $html;
    }
}
