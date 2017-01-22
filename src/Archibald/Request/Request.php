<?php

namespace Archibald\Request;

use Archibald\Request\Custom as CustomRequest;
use Archibald\Request\Remember as RememberRequest;
use Archibald\Request\ReplyGif as ReplyGifRequest;
use Archibald\Remember\Remember as RememberDatabase;
use GuzzleHttp\Client;

/**
 * Class Request
 *
 * @package Archibald\Request
 */
class Request
{
    private $teamId;
    private $tag;
    private $channel;
    private $user;
    private $userId;

    /**
     * @var Client
     */
    private $client;

    private $useRemember = true;

    /**
     * Request constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        if ($request['token'] && $request['command']) {
            $token      = $request['token'];
            $command    = $request['command'];

            if ($this->isValidToken($token)) {
                $this->init($request);
            } else {
                $this->postAsSlackBot("Invalid Slash Command Token… Trying to do some hazy stuff, huh? d–(^ ‿ ^ )z\n" .
                "Please check your config.php!");
            }
        }

        $this->postAsSlackBot("Shit! ʕノ•ᴥ•ʔノ ︵ ┻━┻ Please forgive me, sometimes I use words.\n" .
            "(No valid API call).");
    }

    /**
     * @param $request
     */
    public function init($request)
    {
        $this->teamId  = $request['team_id'];
        $this->channel = $request['channel_id'];
        $this->userId  = $request['user_id'];
        $this->user    = $request['user_name'];
        $this->tag     = $request['text'];

        $this->client = new Client();
        $this->parseRequest();
    }

    /**
     * Parses request body and check what needs to be done.
     */
    public function parseRequest()
    {
        // If command starts with "remember", we use the remember feature
        $remember = new RememberDatabase();

        if ($remember->useRemember()) {
            if (preg_match('/^(remember\s)/', $this->tag)) {
                $this->saveTag();
            } elseif (preg_match('/^(remembered$)/', $this->tag)) {
                $this->getRemembered();
            }
        } else {
            $this->useRemember = false;
        }

        // Sort out some other commands
        switch ($this->tag) {
            case 'tags':
                $this->getTags();
                break;

            /**
             * Show help.
             *
             * Can’t use 'help' as a command, because help is a tag
             */
            case ' ':
            case '':
            case 'howto':
            case 'manual':
                $this->showHelp();
                break;

            default:
                $this->searchGif();
                break;
        }
    }

    public function saveTag()
    {
        $remember = new RememberRequest();
        $result = $remember->saveTag($this->tag, $this->user, $this->userId);

        if ($this->isError($result)) {
            $this->postAsSlackBot($result->getMessage());
            return;
        }

        $this->postAsSlackBot('Ha! You can now use *"' . implode('"* or *"', $result['tags']) .
          '"* as tags to run that masterpiece from `' . $result['url'] . '`. ' .
          'Nobody will know :wink:');
    }

    public function getRemembered()
    {
        $remember = new RememberRequest();
        $remembered = $remember->getRemembered();

        if ($this->isError($remembered)) {
            if ($remembered->isErrorOfType('not-found')) {
                $this->postAsSlackBot("I haven’t found any remembered tags. ʕノ•ᴥ•ʔノ ︵ ┻━┻  Add your first one with:\n" .
                    "`/archie remember your, tags, separated, through, commas" .
                    " = https://example.com/url-to-gif-file.gif`");
            } elseif ($remembered->isErrorOfType('database')) {
                $this->postAsSlackBot("Ooooops! I have a database error:\n:bomb: `" . $remembered->getMessage() . '`');
            }

            $this->postAsSlackBot($remembered->getMessage());
        }

        $this->postAsSlackBot($this->getTagList($remembered));
    }

    public function getTags()
    {
        $replygif = new ReplyGifRequest();
        $replygifTags = $total = $replygif->getTags();

        if ($this->useRemember) {
            $remember = new RememberRequest();
            $rememberTags = $remember->getRemembered();

            if (!RequestError::isError($rememberTags)) {
                $total = [];

                /**
                 * Merge arrays and sum up the values.
                 * See http://stackoverflow.com/a/6086409/1059980.
                 */
                foreach (array_keys($replygifTags + $rememberTags) as $key) {
                    $total[$key] = (isset($replygifTags[$key])
                        ? $replygifTags[$key]
                        : 0) + (isset($rememberTags[$key])
                        ? $rememberTags[$key]
                        : 0);
                }

            }
        }

        // Sort by tag
        uksort($total, 'strnatcasecmp');

        // The Tag List is echoed by slackbot, so other don’t see it
        $text = $this->getTagList($total);

        $this->postAsSlackBot($text);
    }

    /**
     * Search Gifs
     */
    private function searchGif()
    {
        $customRequest = new CustomRequest();
        $custom = $customRequest->getCustom($this->tag);

        if (!empty($custom) && is_array($custom)) {
            $gif = $this->getRandomGif($custom);

            if ($gif) {
                $this->postToSlack($gif);
                return;
            }
        }

        if ($this->useRemember) {
            $remember = new RememberRequest();
            $gifs = $remember->getGifs($this->tag);
        } else {
            $gifs = [];
        }


        if ($this->isError($gifs)) {
            if ($gifs->isErrorOfType('not-found')) {
                $gifs = [];
            } else {
                $this->postAsSlackBot($gifs->getMessage());
                return;
            }
        }

        $replygif = new ReplyGifRequest();
        $replygifs = $replygif->getGifs($this->tag);

        if (empty($gifs) && $this->isError($replygifs)) {
            if ($replygifs->isErrorOfType('not-found')) {
                $this->postAsSlackBot("Oh noes! I couldn’t find any GIFs with tag \"*{$this->tag}*\"\n" .
                "Pleeeeease, try `/archie tags` for a list of tags you can use.");
            } else {
                $this->postAsSlackBot($replygifs->getMessage());
            }

            return;
        }

        $gifs = array_merge($gifs, $replygifs);
        $gif = $this->getRandomGif($gifs);

        $this->postToSlack($gif);
    }

    /**
     * Returns a random gif from an array of gifs
     *
     * @param array $gifs   Gifs
     *
     * @return array|bool
     */
    public function getRandomGif($gifs)
    {
        $size = count($gifs);

        if ($size < 1) {
            return false;
        }

        if ($size === 1) {
            return $gifs[0];
        }

        return $gifs[array_rand($gifs)];
    }

    /**
     * Generates a list of tags that is ready to be echoed to Slack.
     *
     * @param array $tags Tags to output
     *
     * @return string Taglist
     */
    public function getTagList($tags)
    {
        $tagList = "Type `/archie ` with one of the following tags (number of GIFs in braces):\n";

        foreach ($tags as $tag => $count) {
            $tagList .= "*{$tag}* ({$count})\t";
        }

        return $tagList;
    }

    /**
     * Post instructions on how to use Archibald.
     */
    public function showHelp()
    {
        $message = "This is Archibald! Here’s a list of commands you can use:\n\n" .
            "*`/archie tags`* Shows a list of all tags that can be used.\n\n" .
            "*`/archie [tag]`* Replace [tag] with a tag to let Archibald search for all Gifs with that tag " .
            "and randomly select one for you.\n" .
            "Examples: `/archie wow`, or `/archie please` or `/archie oh my god`\n\n";

        if ($this->useRemember) {
            $message .= "*`/archie remember`* Tell archie to save your own tags.\n" .
            "Scheme: `/archie remember your, tags, separated, through, commas" .
            " = https://example.com/url-to-gif-file.gif`\n" .
            "Example: `/archie remember fabulous, kiss, wink = http://i.giphy.com/3o85xrhcwk5SnS8bvi.gif`\n\n" .
            "*`/archie remembered`* Show a list of all tags that somebody remembered.\n\n" .
            "Ready? KTHXBYE!";
        }

        $this->postAsSlackBot($message);
    }

    /**
     * Post a message to to the current Slack channel as Slackbot.
     *
     * Everything that is echoed from a Webhook is echoed into the current
     * Slack channel (where the Slash Command originated) by Slackbot.
     *
     * @param string $text Text to post.
     */
    public function postAsSlackBot($text)
    {
        echo $text;

        // Make sure no other messages are posted
        die();
    }

    /**
     * Posts a message back to Slack.
     *
     * @param string|array $gif     Gif in an array definition or just the URL to the gif.
     * @param string       $message An additional message or text to be appended to the gif
     *
     * @internal param $url
     * @internal param string $text Text or URL to post
     */
    public function postToSlack($gif, $message = '')
    {
        if (empty($message)) {
            $message = $this->tag;
        }

        if (is_array($gif)) {
            $url = $gif['url'];
            $message = !empty($gif['text']) ? $gif['text'] : '';
        } else {
            $url = $gif;
        }

        $finalMessage = "<{$url}|{$message}>";

        $data = [
            'payload' => json_encode([
                'username'   => $this->user,
                'icon_emoji' => ':hatched_chick:',
                'channel'    => $this->channel,
                'text'       => $finalMessage,
            ])
        ];

        $this->client->post(WEBHOOK_URL, [
            'body' => $data
        ]);

        // Make sure no other messages are posted
        die();
    }

    /**
     * Checks if a variable is of type RequestError
     *
     * @param mixed $thing  The variable to check
     *
     * @return bool
     */
    private function isError($thing)
    {
        return ($thing instanceof RequestError);
    }

    /**
     * Checks if the token the request is made is the same as saved in the config.
     *
     * @param string $token
     *
     * @return bool
     */
    private function isValidToken($token)
    {
        return $token == SLASHCOMMAND_TOKEN;
    }
}
