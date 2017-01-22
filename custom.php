<?php

/**
 * Array of custom images.
 *
 * If you can’t use a database or want to define some tags statically, you can do it here.
 * Check the given examples on how to use it.
 *
 * @return array
 */
function get_custom()
{
    return [
        [
            'tags' => ['shaq', 'shaquille', 'love', 'kiss'],
            'url' => 'http://replygif.net/i/1106.gif',
            'text' => 'I love you',
        ], [
            'tags' => ['kannste', 'kannsteschonsomachen', 'kannstemachen', 'kacke'],
            'url' => 'http://i.imgur.com/D6iqV0b.png',
        ],
    ];
}
