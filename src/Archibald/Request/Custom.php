<?php

namespace Archibald\Request;

class Custom
{
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
