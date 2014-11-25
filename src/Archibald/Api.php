<?php

namespace Archibald;

use Archibald\Request;

class Api
{
	private $slashToken = SLASHTOKEN;
	private $commandName = '/archie';

	public function __construct($request)
	{
		if ($request['token'] && $request['command']) {
			$token      = $request['token'];
			$command    = $request['command'];

			if ($this->isValidToken($token) && $this->isValidCommand($command)) {

				$data = array(
					'team_id'   => $request['team_id'],
					'channel'   => $request['channel_id'],
					'user_id'   => $request['user_id'],
					'user'      => $request['user_name'],
					'body'      => $request['text']
				);

				$request = new Request($data);
			}
			else {
				echo 'Invalid Token or Command';
			}
		}
		else {
			echo 'No valid API call.';
		}
	}

	private function isValidToken($token)
	{
		return $token == $this->slashToken;
	}

	private function isValidCommand($command)
	{
		return $command == $this->commandName;
	}
}
