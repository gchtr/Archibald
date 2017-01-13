<?php

namespace Archibald\Request;

use Archibald\Remember\Remember as RememberDatabase;

class Remember
{
    public function getGifs($tag)
    {
        $remember = new RememberDatabase();
        $results = $remember->getRemember($tag);

        if ($results && !empty($results)) {
            return $results;
        }

        return new RequestError('not-found');
    }

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

    private function isImageUrl($url)
    {
        return preg_match('/\.(jpg|jpeg|png|gif)$/', $url);
    }
}
