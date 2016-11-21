<?php

namespace Archibald;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Request
{
    private $webhookUrl;

    private $body;
    private $channel;
    private $user;

    private $requestGifs = 'http://replygif.net/api/gifs';
    private $requestTags = 'http://replygif.net/api/tags';
    private $apiKey = '39YAprx5Yi';

    private $client;

    public function __construct($request)
    {
        $this->webhookUrl = WEBHOOK_URL;

        $this->body = $request['body'];
        $this->channel = $request['channel'];
        $this->user = $request['user'];
        $this->userId = $request['user_id'];

        $this->client = new Client();

        $this->parseRequestType();
    }

    public function parseRequestType()
    {
        /**
         * If command starts with "remember", we use the
         * remember feature
         */
        if (preg_match('/^(remember\s)/', $this->body)) {
            $this->remember($this->body);
        } elseif (preg_match('/^(remembered$)/', $this->body)) {
            $this->remembered();
        } else {
            switch ($this->body) {
                case 'shaq':
                    $this->body = 'I love you!';
                    $this->staticRequest('shaq');
                    break;

                case 'kannste':
                case 'kannsteschonsomachen':
                case 'kannstemachen':
                case 'kacke':
                    $this->staticRequest('kannste');
                    break;

                case 'tags':
                    $this->searchTags();
                    break;

                case '':
                    echo 'Please provide a tag! e.g. `/archie wow`';
                    break;

                default:
                    $this->searchGif();
                    break;
            }
        }
    }

    private function staticRequest($request)
    {
        $responseBody = '';

        switch ($request) {
            case 'shaq':
                $responseBody = 'http://replygif.net/i/1106.gif';
                break;
            case 'kannste':
                $responseBody = 'http://i.imgur.com/D6iqV0b.png';
                break;
        }

        $this->postResponse($responseBody);
    }

    private function remember($string)
    {
        $command = substr($string, strlen('remember '));

        $splitCommand = explode('=', $command);

        $tags = explode(',', $splitCommand[0]);
        $url = $splitCommand[1];

        if (is_array($tags) && !empty($tags)) {
            if ($this->isImageUrl($url)) {
                $remember = new Remember();
                $remember->saveRemember($tags, $url, $this->user, $this->userId);
            } else {
                echo 'Aaah, provide with me raw imageeeees, GIFs are highly preferred! Aye!';
                die();
            }
        } else {
            echo 'I need at least one tag to work with.';
            die();
        }
    }

    private function remembered()
    {
        $remember = new Remember();
        $remembered = $remember->getRemembered();
        $remembered = array_count_values($remembered);

        $tagList = '';

        foreach ($remembered as $tag => $count) {
            $tagList .= $tag . " (" . $count . ")\t";
        }

        /**
         * The Tag List is echoed by slackbot,
         * so other don’t see it
         */
        echo $tagList;
    }

    private function searchGif()
    {
        if (!$this->searchRemember()) {
            $this->searchReplyGif();
        }
    }

    private function searchRemember()
    {
        $remember = new Remember();
        $results = $remember->getRemember($this->body);

        if ($results && !empty($results)) {
            $message = $this->randomGif($results, false);

            if (false !== $message) {
                $this->postResponse($message['archie']);
                return true;
            } else {
                echo 'No GIFs found with tag *' . $this->body . '*';
                return false;
            }
        }
        return false;
    }

    private function searchReplyGif()
    {
        try {
            $response = $this->client->get(
                $this->requestGifs,
                [
                    'query' => [
                        'api-key' => $this->apiKey,
                        'tag' => $this->body
                    ]
                ]
            );

            $responseBody = $response->getBody();
        } catch (RequestException $e) {
            echo $e->getRequest();
            if ($e->hasResponse()) {
                $this->postResponse($e->getResponse());
            }
        }

        if (!empty($responseBody)) {
            $message = $this->randomGif($responseBody);
        }

        if (isset($message) && false !== $message && property_exists($message, 'file')) {
            $this->postResponse($message->file);
        } else {
            echo 'No GIFs found with tag *' . $this->body . '*';
            die();
        }
    }

    public function searchTags()
    {
        try {
            $response = $this->client->get(
                $this->requestTags,
                [
                    'query' => [
                        'api-key' => $this->apiKey,
                        'reaction' => 1
                    ]
                ]
            );

            $responseBody = $response->getBody();
            $this->getTagList(json_decode($responseBody));
        } catch (RequestException $e) {
            echo $e->getRequest();
            if ($e->hasResponse()) {
                $this->postResponse($e->getResponse());
            }
        }
    }

    public function randomGif($gifs, $isJson = true)
    {
        if ($isJson) {
            $gifs = json_decode($gifs);
        }

        $size = count($gifs);
        $randomIndex = rand(0, $size-1);

        if ($size < 1) {
            return false;
        }

        return $gifs[$randomIndex];
    }

    public function getTagList($tags)
    {
        $tagList = '';

        foreach ($tags as $tag) {
            $tagList .= $tag->title . " (" . $tag->count . ")\t";
        }

        // The Tag List is echoed by slackbot, so other don’t see it
        echo $tagList;
    }

    public function postResponse($message)
    {
        $finalMessage = $this->user . ": <" . $message . "|" . $this->body . ">";
        $channel = $this->channel;

        $data = array(
            'payload' => json_encode(array(
                'username' => 'Archibald',
                'icon_emoji' => ':hatched_chick:',
                'channel' => $channel,
                'text' => $finalMessage
            ))
        );

        $this->client->post($this->webhookUrl, array(
            'body' => $data
        ));
    }

    private function isImageUrl($url)
    {
        return preg_match('/\.(jpg|jpeg|png|gif)$/', $url);
    }
}
