# 💾  PHP API Client

![visitors](https://visitor-badge.laobi.icu/badge?page_id=exbil.-php-api)
![](https://img.shields.io/badge/stable-v.1.0-informational?style=flat&logoColor=white&color=6aa6f8)
![](https://img.shields.io/badge/license-MIT-informational?style=flat&logoColor=white&color=6aa6f8)

[![Latest Stable Version](http://poser.pugx.org/exbil/-php-api/v)](https://packagist.org/packages/exbil/-php-api) [![Total Downloads](http://poser.pugx.org/exbil/-php-api/downloads)](https://packagist.org/packages/exbil/-php-api) [![Latest Unstable Version](http://poser.pugx.org/exbil/-php-api/v/unstable)](https://packagist.org/packages/exbil/-php-api) [![License](http://poser.pugx.org/exbil/24fire-php-api/license)](https://packagist.org/packages/vexura/24fire-api) [![PHP Version Require](http://poser.pugx.org/exbil/24fire-php-api/require/php)](https://packagist.org/packages/exbil/24fire-php-api)

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

// Then simply pass your API-Token when creating the API client object.
$token = getenv('24FIRE_API_KEY');
$client = new FireAPI($token);

// Then you are able to perform a request
var_dump($client->servers()->getServers());
?>
```