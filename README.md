﻿# 💾  PHP API Client

![visitors](https://visitor-badge.laobi.icu/badge?page_id=exbil.-php-api)
![](https://img.shields.io/badge/stable-v.1.0-informational?style=flat&logoColor=white&color=6aa6f8)
![](https://img.shields.io/badge/license-MIT-informational?style=flat&logoColor=white&color=6aa6f8)

[![Latest Stable Version](http://poser.pugx.org/exbil/-php-api/v)](https://packagist.org/packages/exbil/-php-api) [![Total Downloads](http://poser.pugx.org/exbil/-php-api/downloads)](https://packagist.org/packages/exbil/-php-api) [![Latest Unstable Version](http://poser.pugx.org/exbil/-php-api/v/unstable)](https://packagist.org/packages/exbil/-php-api) [![License](http://poser.pugx.org/exbil/24fire-php-api/license)](https://packagist.org/packages/vexura/24fire-api) [![PHP Version Require](http://poser.pugx.org/exbil/24fire-php-api/require/php)](https://packagist.org/packages/exbil/24fire-php-api)

> [!NOTE]
> Disclaimer: If you have a suggestion for a better name for a route, feel free to send your proposal via email
> to [composer@exbil.net](mailto:composer@exbil.net) or create a pull request.

# Getting Started
### Requirements
* [**PHP 8.4+**](https://www.php.net/downloads.php)
* Extensions: [Composer](https://getcomposer.org/), [PHP-JSON](https://www.php.net/manual/en/book.json.php)

# ⚒️ Install
In the root of your project execute the following:
```sh
composer require exbil/24fire-php-api
```
or add this to your `composer.json` file:
```json
{
  "require": {
    "exbil/24fire-php-api": "^1.0"
  }
}
```

Then perform the installation:
```sh
$ composer install --no-dev
```

# 📑 Usage

Search for the official API Documentation [here](https://docs.fireapi.de/).  
You need an [API Key](https://24fire.de/reselling/) for that.

### 🗃️ Basic

```php
<?php
// Require the autoloader
require_once 'vendor/autoload.php';

// Use the library namespace
use FireAPI\FireAPI;

// Your API token
$token = getenv('24FIRE_API_KEY');

// Create the API client with the sandbox option
$client = new FireAPI($token, true); // 'true' activates the sandbox environment

// Request to the server in the sandbox environment
var_dump($client->rootServer()->getAll());
?>
```