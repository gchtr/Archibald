<?php

namespace Archibald\Request;

/**
 * Class Custom
 *
 * @package Archibald\Request
 */
class Custom
{
    /**
     * Tries to find a tag in the list of custom images.
     *
     * @param string $tag The tag to look for
     *
     * @return array|bool
     */
    public function getCustom($tag)
    {
        $results = [];

        foreach (get_custom() as $static) {
            if (in_array($tag, $static['tags'], true)) {
                $result = [
                    'url' => $static['url']
                ];

                if (isset($static['text'])) {
                    $result['text'] = $static['text'];
                }

                $results[] = $result;
            }
        }

        if (!empty($results)) {
            return $results;
        }

        return false;
    }
}
