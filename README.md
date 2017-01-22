# Archibald

Archibald is a Slack integration written in PHP to post tag-selected GIF replies from [replygif.net](http://replygif.net) into your current Slack channel or Direct Messages. You can either self-host it or set it up with the *[Heroku Deploy Button](#deploy-to-heroku)*.

## How to use Archibald

![](https://cloud.githubusercontent.com/assets/2084481/22185882/a7cd9084-e0ed-11e6-8558-9f6cc22df94b.gif)

With Archibald, you can use the following commands in Slack:

| Command                   | Description                                   |
|---                        |---                                            |
| `/archie tags`            | Shows a list of all tags that can be used, together with the amount of gifs available in brackets |
| `/archie [tag]` | Replace [tag] to let Archibald search for a gifs and randomly select one for you.<br>E.g: `/archie magic` |

### The Remember feature

Archibald lets you save your own images and gifs together with a list of tags.

| Command                   | Description                                   |
|---                        |---                                            |
| `/archie remember` | Save an new image together with tags.<br><br>Example:<br>`/archie remember fabulous, kiss, wink = http://i.giphy.com/3o85xrhcwk5SnS8bvi.gif` |
| `/archie remembered` | Show a list of all tags that somebody remembered. |

To use the Remember feature, you need to set up your Remember database.

## Get Archibald up and running

Take the following steps to set up and configure Archibald for your Slack team:

1. Configure [Slash Command](#slash-command-integration) and [Incoming WebHooks](#incoming-webhooks) integrations in your Custom Integration settings of your Slack team settings.
2. [Configure Archibald](configure-archibald-through-config-php) through `config.php`. Optionally configure the [Remember database](#setup-remember-database-optional) and [Custom Tags](#configure-custom-tags-optional).
3. Deploy Archibald either [to your own server](#deploy-to-your-own-php-server) or use the [Heroku Button](#deploy-to-heroku).
4. Check if everything is set up correctly by visiting `https://url-to-your-archibald.example.com/api.php`

---

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

---

### Incoming WebHooks Integration

Now add a new **Incoming WebHooks** integration for your team.

![](https://cloud.githubusercontent.com/assets/2084481/5192319/cb321104-74f6-11e4-90ac-1e952a176534.png)

It doesn’t matter which channel you choose for the messages to be posted to. All you need to do in the settings for the **Incoming WebHooks** integration is to copy the **Webhook URL**. You will use it when you configure Archibald. All other values of the Incoming WebHooks integration will be overwritten by Archibald.

![](https://cloud.githubusercontent.com/assets/2084481/5192055/5b4c7138-74f4-11e4-9e71-5597f30672fe.png)

---

### Configure Archibald through config.php

Rename `config.sample.php` to `config.php` and set the *Token* from the **Slash Command** integration as well as the *Webhook URL* from the **Incoming WebHooks** integration.

```php
/**
 * Slash Command Token
 *
 * You will get a token when you add a new Slash Command integration in your Slack Integration settings.
 */
define('SLASHCOMMAND_TOKEN', 'Your copied Token here');

/**
 * Webhook URL
 *
 * You will find your Webhook URL when you add a new Incoming WebHook integration in your Slack Integration settings.
 */
define('WEBHOOK_URL', 'The copied WebHook URL here');
```

---

### Setup Remember database (optional)

In order to use the Remember feature, you need to setup your own database. Archibald supports two types of database:

* **SQL** – SQL Database (MySQL, Postgres, SQL Server or SQLite)
* **JSON** – Database based on JSON files. For this you need a persistent hosting for your files.

#### SQL Database

In your `config.php`:

```php
// Example for MySQL database
define('DB_TYPE', 'SQL');

define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'archibald');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_PREFIX', '');
```

#### JSON Database

In your `config.php`:

```php
define('DB_TYPE', 'JSON');
```

## Configure custom tags (optional)

Custom Tags are kind of the third option to bring in additional gifs to Archibald. Rename `custom.sample.php` to `custom.php` and add your own tags.

Custom Tags will not appear when you use `/archie tags` (yet).

---

## Deploy Archibald

You can either deploy Archibald to your own server or use the Heroku Button.

### Deploy to your own PHP Server 

* You need to upload all files to a webserver running PHP version 5.4.x or higher.
* Be sure to use [Composer](https://getcomposer.org/) to include all vendor files.

```sh
composer install
```

---

### Deploy to Heroku

You can use the Heroku Deploy Button to setup Archibald on Heroku:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

When you press that button, you will be redirected to Heroku, where you can set up your own app. Insert the *Token* from the **Slash Command** integration and the *Webhook URL* from the **Incoming WebHooks** integration into the respective form fields and hit *Deploy*.

If you’re finished with the deployment, you can go and view the app.

![](https://cloud.githubusercontent.com/assets/2084481/8762562/f5ec5898-2d7c-11e5-945c-bd8b6786a0c2.png)

Now head over to your Slack Integration settings and insert the URL you’ll be given by the app into the URL field of the **Slash Command** integration.

![](https://cloud.githubusercontent.com/assets/2084481/8761294/eb2c06ca-2d49-11e5-9d93-0c345706a658.png)

That’s it. You can now try out Archibald.

## FAQ

### Why not use Giphy?

Giphy is just random gifs, which often don’t really fit as a response. [ReplyGif](http://replygif.net/) is a collection of gifs especially curated to be used as replies in a conversation and provides much more fun. Promise!

### Why self-hosted?

I am not that serious about it that I want to setup a dedicated server for Archibald as a service. Plus this way, it’s your data. Maybe I’m also just lazy.
