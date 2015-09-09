<?php

namespace Archibald;

use GuzzleHttp\Client;

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
		}

		else {
			switch ($this->body) {
				case 'shaq':
					$this->body = 'I love you!';
					$shaq = $this->staticRequest('shaq');
					break;

				case 'kannste':
				case 'kannsteschonsomachen':
				case 'kannstemachen':
				case 'kacke':
					$kannste = $this->staticRequest('kannste');
					break;

				case 'tags':
					$tags = $this->searchTags($this->body);
					break;

				case '';
					echo 'Please provide a tag! e.g. `/archie wow`';
					break;

				default:
					$search = $this->searchGif($this->body);
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
				$remember->saveRemember($tags, $url);
			}
			else {
				echo 'Aaah, provide with me raw imageeeees, GIFs are highly preferred! Aye!';
				die();
			}
		}
		else {
			echo 'I need at least one tag to work with.';
			die();
		}
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
		$result = $remember->getRemember($this->body);

		if ($result) {
			$this->postResponse($result);
			return true;
		}
		return false;
	}

	private function searchReplyGif()
	{
		try {
			$response = $this->client->get(
				$this->requestGifs, [
					'query' => [
						'api-key' => $this->apiKey,
						'tag' => $this->body
					]
				]
			);
		}
		catch (RequestException $e) {
			echo $e->getRequest();
			if ($e->hasResponse()) {
				$this->postResponse($e->getResponse());
			}
		}
		$responseBody = $response->getBody();
		$message = $this->randomGif($responseBody);

		if (false !== $message) {
		  $this->postResponse($message);
		}
		else {
			echo 'No GIFs found with tag *' . $this->body . '*';
			die();
		}
	}

	public function searchTags($requestString)
	{
		try {
			$response = $this->client->get(
				$this->requestTags, [
					'query' => [
						'api-key' => $this->apiKey,
						'reaction' => 1
					]
				]
			);
		}
		catch (RequestException $e) {
			echo $e->getRequest();
			if ($e->hasResponse()) {
				$this->postResponse($e->getResponse());
			}
		}

		$responseBody = $response->getBody();
		$message = $this->getTagList($responseBody);
	}

	public function randomGif($responseBody)
	{
		$gifs = json_decode($responseBody);

		$size = count($gifs);
		$randomIndex = rand(0, $size-1);

		if ($size < 1) {
			return false;
		}
		return $gifs[$randomIndex]->file;
	}

	public function getTagList($responseBody)
	{
		$tags = json_decode($responseBody);

		$tagList = '';

		foreach ($tags as $tag) {
			$tagList .= $tag->title . " (" . $tag->count . ")\t";
		}

		/**
		 * The Tag List is echoed by slackbot,
		 * so other don’t see it
		 */
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

		$request = $this->client->post($this->webhookUrl, array(
			'body' => $data
		));
	}

	private function isImageUrl($url)
	{
		return preg_match('/\.(jpg|jpeg|png|gif)$/', $url);
	}
}
