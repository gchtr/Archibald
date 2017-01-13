<?php

namespace Archibald;

use Archibald\Remember\Remember;

class Archibald
{
    /**
     * The filename to check for in the root directory.
     */
    public $configName = 'config.php';

    /**
     * The configuration variables needed for the API to work.
     *
     * @var array
     */
    public $configVars = [
        'SLASHCOMMAND_TOKEN',
        'WEBHOOK_URL'
    ];

    /**
     * Path to the config file.
     *
     * @var string
     */
    public $configPath;

    /**
     * Array for all configuration errors
     * @var array
     */
    private $configErrors = [];

    private $messages = [];

    /**
     * Checks if a config file is present and loads it.
     *
     * @return boolean  Returns true if the config was found and loaded
     */
    public function loadConfig()
    {
        if ($this->hasConfig()) {
            $this->configPath = DOCUMENT_ROOT . '/' . $this->configName;
            require_once($this->configPath);

            return true;
        }

        return false;
    }

    /**
     * Checks if the config file exists in the document root.
     *
     * @return boolean  Returns true if file exists.
     */
    private function hasConfig()
    {
        return file_exists(DOCUMENT_ROOT . '/' . $this->configName);
    }

    /**
     * Loops through the config variables and populates $configErrors if there are any.
     */
    public function setupConfigVars()
    {
        foreach ($this->configVars as $configVar) {
            $result = $this->checkConfigVar($configVar);

            if ($result !== true) {
                $this->setConfigError($result);
            }
        }
    }

    /**
     * Checks if a configuration variable is defined and not empty.
     * Environment variables take precedence over defined constants in config.php
     *
     * @param string    $configVar
     *
     * @return bool|string
     */
    private function checkConfigVar($configVar)
    {
        $check = getenv($configVar);
        $const = null;

        if ($check !== false) {
            if (empty($check)) {
                return $configVar . ' is empty. Please make sure you have properly set your configuration variables.';
            }

            return define($configVar, $check);
        } elseif (defined($configVar)) {
            $const = constant($configVar);

            if (empty($const)) {
                return $configVar . ' is empty. Please make sure you have your config.php set up properly';
            }

            return true;
        } else {
            return $configVar . ' was not found.'
            . 'Please make sure you have it defined either in your config.php or as a Config Variable.';
        }
    }

    public function setConfigError($error, $type = '')
    {
        if (!empty($type)) {
            $this->configErrors[] = [
                'error' => $error,
                'type' => $type,
            ];
        } else {
            $this->configErrors[] = $error;
        }
    }

    /**
     * Checks if there are any config errors.
     *
     * @return boolean  Returns true if there are errors
     */
    public function hasConfigErrors()
    {
        return !empty($this->configErrors);
    }

    /**
     * Returns config errors.
     *
     * @return array|null
     */
    public function getConfigErrors()
    {
        return $this->configErrors;
    }

    public function setMessage($message)
    {
        return $this->messages[] = $message;
    }

    public function hasMessages()
    {
        return !empty($this->messages);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Initalize Remember and create Database if it doesn’t exist.
     */
    public function setupRemember()
    {
        $remember = new Remember();

        if (!$remember->useRemember()) {
            return;
        }

        $result = $remember->createDatabaseIfNotExists();

        if ($result instanceof \Exception) {
            $this->setConfigError($result->getMessage(), 'Database Error');
        } elseif (true === $result) {
            return;
        }

        $this->setMessage($result);
    }
}
