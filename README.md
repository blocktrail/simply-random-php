Simply Random
=============
[![Latest Stable Version](https://badge.fury.io/ph/blocktrail%2Fsimply-random.svg)](https://packagist.org/packages/blocktrail/simply-random)
[![Build Status](https://travis-ci.org/blocktrail/simply-random-php.svg?branch=master)](https://travis-ci.org/blocktrail/simply-random-php)

tested on **PHP 5.4**, **5.5**, **5.6** and **HHVM**

## What is this library?
This is a simple library for random number generation.

This library can generate strong random numbers and cryptographically secure random numbers.


## Dependencies
This library depends on `ext-mcrypt` as it uses `mcrypt_create_iv($length, \MCRYPT_DEV_URANDOM)` to get random data from `/dev/urandom`.


## Installation
Simply use `composer require blocktrail/simply-random ~1.0`.


## Usage
Please refer to `test.php` for example usage.


## Credits to ircmaxell
Most of the code used in this library was originally written by [ircmaxell](https://github.com/ircmaxell) 
for [random_compat](https://github.com/ircmaxell/random_compat).

I stripped out some complexity from his library which I deemed unnecessary.  
Such as using various sources of random bytes (`mcrypt`, `/dev/random`, `/dev/urandom` , `openssl_random_pseudo_bytes`) and mixing those.  
When using `/dev/urandom` it shouldn't be neccesary to do mixing, half the world relies on `/dev/urandom` being secure, 
not to mention that `mcrypt` and `openseel_random_pseudo_bytes` both use `/dev/urandom` under the hood...
