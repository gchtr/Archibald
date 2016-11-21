<?php

namespace Archibald;

class Api
{
    private $slashToken = '';
    private $commandName = '/archie';

    public function __construct($request)
    {
        $this->slashToken = SLASHCOMMAND_TOKEN;

        if ($request['token'] && $request['command']) {
            $token      = $request['token'];
            $command    = $request['command'];

            if ($this->isValidToken($token)) {
                if ($this->isValidCommand($command)) {

                    $data = array(
                        'team_id'   => $request['team_id'],
                        'channel'   => $request['channel_id'],
                        'user_id'   => $request['user_id'],
                        'user'      => $request['user_name'],
                        'body'      => $request['text']
                    );

                    new Request($data);
                } else {
                    echo 'Invalid Command';
                }
            } else {
                echo 'Invalid Slash Command Token. Please check your config!';
            }
        } else {
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
