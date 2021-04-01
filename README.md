# seat-fitting
A module for [SeAT](https://github.com/eveseat/seat) that holds fittings and can compare the required skills for a fit to your character.

[![Latest Stable Version](https://img.shields.io/packagist/v/denngarr/seat-fitting.svg?style=flat-square)]()
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](https://raw.githubusercontent.com/dysath/seat-srp/master/LICENSE)

If you have issues with this, you can contact me on Eve as **Crypta Electrica**, or on the [https://eveseat.github.io/docs/about/contact/](SeAT Discord)

## Quick Installation:

In your seat directory (By default:  /var/www/seat), type the following:

```
php artisan down
composer require denngarr/seat-fitting

php artisan vendor:publish --force --all
php artisan migrate

php artisan up
```

And now, when you log into 'Seat', you should see a 'Fittings' link on the left.

Good luck, and Happy Hunting!!  o7

## Usage Tracking

In order to get an idea of the usage of this plugin, a very simplistic form of anonymous usage tracking has been implemented.

Read more about the system in use [here](https://github.com/Crypta-Eve/snoopy)


