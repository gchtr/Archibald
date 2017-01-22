<?php

namespace Archibald\Request;

use Archibald\Remember\Remember as RememberDatabase;

/**
 * Class Remember
 *
 * @package Archibald\Request
 */
class Remember
{
    /**
     * Get all saved Gifs based on a tag.
     *
     * @param string $tag The tag to search for
     *
     * @return RequestError|array|bool An array of tags or error if nothing was found.
     */
    public function getGifs($tag)
    {
        $remember = new RememberDatabase();
        $results = $remember->getRemember($tag);

        if ($results && !empty($results)) {
            return $results;
        }

        return new RequestError('not-found');
    }

    /**
     * Save a new image with under a list of tags.
     *
     * @param string $string The string appended to the remember command that contains the list of tags
     *                       and the URL of the image.
     * @param string $user   The username of the user that executes the command.
     * @param string $userId The user id of the user that executes the command.
     *
     * @return RequestError|array|bool
     */
    public function saveTag($string, $user, $userId)
    {
        $command = substr($string, strlen('remember '));

        if (empty($command)) {
            return new RequestError(
                'input-error',
                "I need to know what I should remember! Here, like this:\n" .
                "`/archie remember fabulous, kiss, wink = http://i.giphy.com/3o85xrhcwk5SnS8bvi.gif`"
            );
        }

        // Tags and URL are separated by '='
        $splitCommand = explode('=', $command);

        // Single tags are divided by comma
        $tags = explode(',', $splitCommand[0]);

        if (isset($splitCommand[1])) {
            $url = $splitCommand[1];
        } else {
            return new RequestError(
                'input-error',
                "Well now. I can’t save a tag only, my dear! I need a URL, too. Look, like this:\n" .
                "`/archie remember approve, nice, thumbsup, impressive = " .
                "https://media.giphy.com/media/XreQmk7ETCak0/giphy.gif`"
            );
        }

        if (!is_array($tags) || empty($tags)) {
            return new RequestError(
                'input-error',
                "Help me here! I need *at least one tag* to work with. Here’s an example:\n" .
                "`/archie remember dog, omg, confused, what? = http://i.giphy.com/fpXxIjftmkk9y.gif`"
            );
        }

        if (!$this->isImageUrl($url)) {
            return new RequestError(
                'input-error',
                "Aaah, provide with me raw imageeeees, pleeeease! Your URL does not look like an image.\n" .
                "GIFs are highly preferred!"
            );
        }

        $remember = new RememberDatabase();
        $result = $remember->saveRemember($tags, $url, $user, $userId);

        if ($result instanceof RequestError) {
            return $result;
        }

        return [
            'tags' => $tags,
            'url' => $url,
        ];
    }

    /**
     * Get all tags that were remembered.
     *
     * @return RequestError|array
     */
    public function getRemembered()
    {
        $remember = new RememberDatabase();
        $remembered = $remember->getRemembered();

        if (RequestError::isError($remembered)) {
            return $remembered;
        }

        $remembered = array_count_values($remembered);
        return $remembered;
    }

    /**
     * Checks if URL might be an image.
     *
     * Of course this check doesn’t guarantee that a file is in fact an image,
     * it just checks if it’s name like one.
     *
     * @param $url
     *
     * @return int
     */
    private function isImageUrl($url)
    {
        return preg_match('/\.(jpg|jpeg|png|gif)$/', $url);
    }
}
