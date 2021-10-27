Todo Logo here

# Laravel Wopi Host

---
Implementation of the WOPI protocol to facilitate intergration with LibreOffice and office online using Laravel.

## 📃 Description

Web Application Open Platform Interface (**WOPI**) protocol let you integrate Office in your web application.

WOPI protocol enables Office for the web to access and change files that are stored in your service.
**Basically it allows you to create Google Docs at the confert of your localhost/application.**

Supports:

* [Collabora Office](#) (Recommended)
* [Office 365](#)

## ⚠ Important

This project is in alpha and under heavy development, please dont use it in production yet ❗

Consider reading [Contribution Guide](#) to help bring this project to life.


Todo Demo

## 🚀 Getting Started

Because every application have diffrent requirements and implementation this project provides the tools and documentation to ease integration.

1. Start by exploring



## Installation

```composer require champs-libres/wopi-bundle```

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this laravel-wopi
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files
3. Remove this block of text.
4. Have fun creating your package.
5. If you need help creating a package, consider picking up our <a href="https://laravelpackage.training">Laravel Package Training</a> video course.
---

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-wopi.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-wopi)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require nagi/laravel-wopi
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Nagi\LaravelWopi\LaravelWopiServiceProvider" --tag="laravel-wopi-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Nagi\LaravelWopi\LaravelWopiServiceProvider" --tag="laravel-wopi-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-wopi = new Nagi\LaravelWopi();
echo $laravel-wopi->echoPhrase('Hello, Nagi!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ahmed Nagi](https://github.com/nagi1)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
