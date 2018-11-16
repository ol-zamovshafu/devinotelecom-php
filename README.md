# PHP Devinotelecom REST API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ol-zamovshafu/devinotelecom-php.svg?style=flat-square)](https://packagist.org/packages/ol-zamovshafu/devinotelecom-php)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package provides an easy to use Devinotelecom SMS service which can be used with both XML and Http apis.

## Contents

- [Installation](#installation)
    - [Setting up the Devinotelecom service](#setting-up-the-devinotelecom-service)
- [Usage](#usage)
    - [Available methods](#available-methods)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install this package via composer:

``` bash
composer require ol-zamovshafu/devinotelecom-php
```

### Setting up the Devinotelecom service

You will need to register to devinotele.com to use this channel.

## Usage

First, boot the Service with your desired client implementation.
- **HttpClient** (This is actually a Rest-Like client but the vendor names their API that way.)

```php
require __DIR__ . '/../vendor/autoload.php';

use Zamovshafu\Devinotelecom\Service;
use Zamovshafu\Devinotelecom\ShortMessageFactory;
use Zamovshafu\Devinotelecom\Http\Clients\HttpClient;

$service = new Service(new HttpClient(
    new GuzzleHttp\Client(),
    'https://integrationapi.net/rest/',
    'username',
    'password',
    'outboxname'
), new ShortMessageFactory());
```

### Available methods

After successfully booting your Service instance up; use one of the following methods to send SMS message(s).

#### One Message - Single or Multiple Recipients:

```php
$response = $service->sendShortMessage(['5530000000', '5420000000'], 'This is a test message.');

if($response->isSuccessful()) {
    // storeGroupIdForLaterReference is not included in the package.
    storeGroupIdForLaterReference($response->groupId());
} else {
    var_dump($response->message());
    var_dump($response->statusCode());
    var_dump($response->status());
}
```

### Cross Reference

`$response->groupId()` will throw BadMethodCallException if the client is `HttpClient`.

change client implementation with caution.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email erdemkeren@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Oleg Lobanov](https://github.com/ol-zamovshafu)

## License

Copyright (c) Hilmi Erdem KEREN erdemkeren@gmail.com

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
