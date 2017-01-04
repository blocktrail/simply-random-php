Simply Random
=============
[![Latest Stable Version](https://badge.fury.io/ph/blocktrail%2Fsimply-random.svg)](https://packagist.org/packages/blocktrail/simply-random)
[![Build Status](https://travis-ci.org/blocktrail/simply-random-php.svg?branch=master)](https://travis-ci.org/blocktrail/simply-random-php)

tested on **5.6**, **7.0**, **7.1** and **HHVM**

## What is this library?
This is a simple library for random number generation using random_bytes.  
This library can generate strong, cryptographically secure random numbers,
and other useful random values.

## Dependencies
This library depends on `random_bytes` or the polyfill provided by ParagonIE. 
If provided as a native extension the kernel CSPRNG is used. The polyfill handles
checking for appropriate sources of entropy if this is not available. 

## Installation
Simply use `composer require blocktrail/simply-random ~1.0`.

## Usage
Please refer to `test.php` for example usage.


## Credits to ircmaxell
Most of the code used in this library was originally written by [ircmaxell](https://github.com/ircmaxell) 
for [random_compat](https://github.com/ircmaxell/random_compat). This library 
initially began as a fork with reduced complexity, and strong dependency on mcrypt.

see:
http://timoh6.github.io/2013/11/05/Secure-random-numbers-for-PHP-developers.html

and more:
https://news.ycombinator.com/item?id=6216101
http://security.stackexchange.com/questions/3936/is-a-rand-from-dev-urandom-secure-for-a-login-key/3939#3939

