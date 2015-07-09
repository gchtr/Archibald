# Archibald

Archibald is a self-hosted Slack integration written in PHP to post tag-selected GIF replies from [replygif.net](http://replygif.net) into your current Slack channel or Direct Messages.

![](https://cloud.githubusercontent.com/assets/2084481/5192177/922eef9a-74f5-11e4-8a4c-f11da8b9f561.gif)

## How to use

`/archie tags`
Shows a list of all tags that can be used, together with the amount of gifs available in brackets

`/archie [tag]`
Use a tag to let Archibald search for a gif with that tag and randomly select one for you.<br>E.g: `/archie magic`

`/archie shaq`
You’ll love it, because he (you know who) loves you dearly!

## Configure Integrations

### Slash Command

Add a new **Slash Command** integration for your team.

![](https://cloud.githubusercontent.com/assets/2084481/5191807/e036b3f2-74f1-11e4-9c5a-385503e0fbfd.png)

For the Integration Settings, use the following values:

| Setting                   | Value                                         |
|---                        |---                                            |
| Command                   | /archie                                       |
| URL                       | http://yourOwnDomain.com/archibald/api.php    |
| Method                    |  POST                                         |
| Autocomplete help text    | ![](https://cloud.githubusercontent.com/assets/2084481/5191903/bdee426e-74f2-11e4-8bcb-61a547cc8fdd.png)            |
| Descriptive Label         | Archibald                                     |

Now copy the Token and paste it as a value for **SLASHCOMMAND_TOKEN** into `config.php`.

![](https://cloud.githubusercontent.com/assets/2084481/5192062/73e9adb4-74f4-11e4-8e9d-e38292b313e2.png)

```php
    define('SLASHCOMMAND_TOKEN', 'Your copied Token here');
```

### Incoming Webhook

Now add a new **Incoming Webhook** integration for your team.

![](https://cloud.githubusercontent.com/assets/2084481/5192319/cb321104-74f6-11e4-90ac-1e952a176534.png)

It doesn’t matter which channel you choose for the messages to be posted to. All you need to do in the settings for the **Incoming Webhook** integration is to copy the **Webhook URL** and paste it as a value for **WEBHOOK_URL** into `config.php`. All other values are overwritten by Archibald.

![](https://cloud.githubusercontent.com/assets/2084481/5192055/5b4c7138-74f4-11e4-9e71-5597f30672fe.png)

```php
    define('WEBHOOK_URL', 'The copied URL here');
```

## Config

Rename `config.sample.php` to `config.php` and set the **Token** for the **Slash Command Integration** as well as the **Webhook URL** from the **Incoming Webhook Integration**.

## Upload files to Webserver

You need to upload all files to a webserver running PHP version 5.4.x or higher.

Before you upload the files, be sure to use [Composer](https://getcomposer.org/) to also include all vendor files.

```sh
composer install
```
## How to run within a Docker container

Build the image

```
docker build -t="jverdeyen/archibald" .
```

Run the image, and replace the token and webhook url

```
docker run -p 80:80 -e SLASHCOMMAND_TOKEN=token -e WEBHOOK_URL="http://webhookurl" -d --name=archibald jverdeyen/archibald
```
