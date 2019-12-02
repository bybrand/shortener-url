# Shortener URL
A dominant URL shortener enabled to many providers. The idea with this extension is your changing between providers without changing lines of the code in the application.

This code is used in production to make short links on [email signatures](https://www.bybrand.io) from the Bybrand.

* Providers supported:: Bitly, and Rebrandly.


## Installation

```
composer require bybrand/shortenerURL
```

## Usage
First, you need to get the API settings from to your provider. Usually, the `domain`, `token`, and `workspace`, and the next is set in class.

Take a example:

```
use Bybrand\ShortenerURL\Provider;
use Bybrand\ShortenerURL\Shorten;
use Bybrand\ShortenerURL\Exception\ShortenFailed;

$provider = new Provider\Bitly([
    'group'  => '',
    'domain' => '',
    'token'  => '',
]);

$shorten = new Shorten($provider);
$shorten->destination('long url');

// If failed return Exception;
$shorten->create();

// Get all returned params.
$result = $shorten->toArray();
```
### Extra methods
Too, you can use auxiliary methods how:

```
// Return a short url.
$shorten->getLink();

// Return the ID from the register in the provider.
$shorten->getId();
```

## Testing

```
bash
$ ./vendor/bin/phpunit
```

or individual method test, by group.

```
bash
$ ./vendor/bin/phpunit --group=Bitly
```

## License

The MIT License (MIT). Please see [License File](https://github.com/bybrand/shortenerURL/blob/master/LICENSE) for more information.
