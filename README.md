# Archibald

Archibald is a Slack integration written in PHP to post tag-selected GIF replies from [replygif.net](http://replygif.net) into your current Slack channel or Direct Messages. You can either self-host it or easily set it up with the **[Heroku Deploy Button](#deploy-to-heroku)**.

## How to use Archibald

![](https://cloud.githubusercontent.com/assets/2084481/5192177/922eef9a-74f5-11e4-8a4c-f11da8b9f561.gif)

With Archibald, you can use the following commands in Slack:

`/archie tags`  
Shows a list of all tags that can be used, together with the amount of gifs available in brackets

`/archie [tag]`  
Use a tag to let Archibald search for a gif with that tag and randomly select one for you.<br>E.g: `/archie magic`

`/archie shaq`  
You’ll love it, because he (you know who) loves you dearly!

## Get Archibald up and running

You will have to take the following steps to set up and configure Archibald for your Slack team:

1. Configure Slash Command and Incoming WebHooks integrations in your Slack settings.
2. Deploy the integration with a Slash Command Token and the Incoming WebHooks URL you are given in step 1.

### Slash Command Integration

Add a new **Slash Command** integration for your team.

![](https://cloud.githubusercontent.com/assets/2084481/5191807/e036b3f2-74f1-11e4-9c5a-385503e0fbfd.png)

For the Integration Settings, use the following values:

| Setting                   | Value                                         |
|---                        |---                                            |
| Command                   | /archie                                       |
| URL                       | `http://yourOwnDomain.com/archibald/api.php` if you use your own server **or** `https://your-heroku-app-name.heroku.com/api.php` if you use Heroku Deploy Button (see below). If you don’t know the URL yet, come back later.   | 
| Method                    |  POST                                         |
| Autocomplete help text    | ![](https://cloud.githubusercontent.com/assets/2084481/5191903/bdee426e-74f2-11e4-8bcb-61a547cc8fdd.png)            |
| Descriptive Label         | Archibald                                     |

You will get a token that you will have to use later to configure Archibald.

![](https://cloud.githubusercontent.com/assets/2084481/5192062/73e9adb4-74f4-11e4-8e9d-e38292b313e2.png)

### Incoming WebHooks Integration

Now add a new **Incoming WebHooks** integration for your team.

![](https://cloud.githubusercontent.com/assets/2084481/5192319/cb321104-74f6-11e4-90ac-1e952a176534.png)

It doesn’t matter which channel you choose for the messages to be posted to. All you need to do in the settings for the **Incoming WebHooks** integration is to copy the **Webhook URL**. You will use it when you configure Archibald. All other values of the Incoming WebHooks integration will be overwritten by Archibald.

![](https://cloud.githubusercontent.com/assets/2084481/5192055/5b4c7138-74f4-11e4-9e71-5597f30672fe.png)

## Deploy to Heroku [deploy-to-heroku]

If you set up Archibald the easy way, you can use the Heroku Deploy Button:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

When you press that button, you will be redirected to Heroku, where you can set up your own app. Insert the *Token* from the **Slash Command** integration and the *Webhook URL* from the **Incoming WebHooks** integration into the respective form fields and hit *Deploy*.

If you’re finished with the deployment, you can go and view the app.

![](https://cloud.githubusercontent.com/assets/2084481/8762562/f5ec5898-2d7c-11e5-945c-bd8b6786a0c2.png)

Now head over to your Slack Integration settings and insert the URL you’ll be given by the app into the URL field of the **Slash Command** integration.

![](https://cloud.githubusercontent.com/assets/2084481/8761294/eb2c06ca-2d49-11e5-9d93-0c345706a658.png)

That’s it. You can now try out Archibald.

## Deploy to your own PHP Server 

### config.php

Rename `config.sample.php` to `config.php` and set the *Token* from the **Slash Command** integration as well as the *Webhook URL* from the **Incoming WebHooks** integration.

```php
    define('SLASHCOMMAND_TOKEN', 'Your copied Token here');
    define('WEBHOOK_URL', 'The copied WebHook URL here');
```

### Upload files to Webserver

You need to upload all files to a webserver running PHP version 5.4.x or higher. Before you upload the files, be sure to use [Composer](https://getcomposer.org/) to also include all vendor files.

```sh
composer install
```
