# Courier Mailjet

A Courier implementation for Mailjet using the v3.1 API.

## Installation

`composer require camuthig/courier-mailjet`

## Usage

Visit [Mailjet](https://app.mailjet.com/transactional) to retrieve your API key and secret.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Camuthig\Courier\Mailjet\MailjetCourier;

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

[link-contributors]: ../../contributors
