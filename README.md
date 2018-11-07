# Courier Mailjet

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travisci]][link-travisci]
[![Coverage Status][ico-codecov]][link-codecov]
[![Style Status][ico-styleci]][link-styleci]
[![Scrutinizer Code Quality][ico-scrutinizer]][link-scrutinizer]

A Courier implementation for Mailjet using the v3.1 API.

## Installation

`composer require camuthig/courier-mailjet`

## Usage

Visit [Mailjet](https://app.mailjet.com/transactional) to retrieve your API key and secret.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Camuthig\Courier\Mailjet\MailjetCourier;
use Mailjet\Client;
use PhpEmail\EmailBuilder;
use PhpEmail\Content\SimpleContent;

$client = new Client(getenv('MAILJET_API_KEY'), getenv('MAILJET_API_SECRET'));
$courier = new MailjetCourier($client);

$email = EmailBuilder::email()
            ->to('to@test.com')
            ->from('from@test.com')
            ->withSubject('Great Email!')
            ->withContent(SimpleContent::text('Text')->addHtml('HTML'))
            ->build();

$courier->deliver($email);
```

### Receipt ID

Mailjet returns a unique ID for each receipient of a message. However, the Courier receipt
API expects a single ID to be returned for each email delivery. To work around this, the
receipt ID returned by this implementation is actually added to the messages as the Custom ID
property.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [Chris Muthig](https://github.com/camuthig)
- [All Contributors][link-contributors]

## License

The Apache License, v2.0. Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/camuthig/courier-mailjet.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat-square
[ico-travisci]: https://img.shields.io/travis/camuthig/courier.svg-mailjet?style=flat-square
[ico-codecov]: https://img.shields.io/scrutinizer/coverage/g/camuthig/courier-mailjet.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/155144704/shield
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/camuthig/courier-mailjet.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/camuthig/courier-mailjet.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/camuthig/courier-mailjet
[link-travisci]: https://travis-ci.org/camuthig/courier-mailjet
[link-codecov]: https://scrutinizer-ci.com/g/camuthig/courier-mailjet
[link-styleci]: https://styleci.io/repos/155144704
[link-scrutinizer]: https://scrutinizer-ci.com/g/camuthig/courier-mailjet
[link-downloads]: https://packagist.org/packages/quartzy/courier-mailjet
[link-contributors]: ../../contributors
