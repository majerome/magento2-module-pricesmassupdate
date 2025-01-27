<h1 align="center">Majerome_PricesMassUpdate</h1> 

<div align="center">
  <p>The PricesMassUpdate module was realized as an exercise to allow updating all product prices in bulk with the same target value via a console command.</p >
  <img src="https://img.shields.io/badge/magento-2.4.6-brightgreen.svg?logo=magento&longCache=true&style=flat-square" alt="Supported Magento Versions" />
  <a href="https://packagist.org/packages/majerome/magento2-module-pricesmassupdate" target="_blank"><img src="https://img.shields.io/packagist/v/majerome/magento2-module-pricesmassupdate.svg?style=flat-square" alt="Latest Stable Version" /></a>
  <a href="https://packagist.org/packages/majerome/magento2-module-pricesmassupdate" target="_blank"><img src="https://poser.pugx.org/majerome/magento2-module-pricesmassupdate/downloads" alt="Composer Downloads" /></a>
  <a href="https://github.com/majerome/magento2-module-pricesmassupdate/pulse/monthly" target="_blank"><img src="https://img.shields.io/badge/maintained%3F-no-red.svg?style=flat-square" alt="Maintained - No" /></a>
  <a href="https://opensource.org/licenses/MIT" target="_blank"><img src="https://img.shields.io/badge/license-MIT-blue.svg" /></a>
</div>

## Table of contents

- [Summary](#summary)
- [Why](#why)
- [Installation](#installation)
- [Usage](#usage)
- [License](#license)

## Summary

This module was inspired by the M.academy course named [Magento Code That Sucks](https://courses.m.academy/courses/2643230/lectures/57143466) created by Mark Shust. The course compares different ways of prices for a large number of products to highlight best practices.

## Why

You should use this module to get a practical example of how to use batch processing to update the price attribute for all products.
This module also explores the creation of a console command, including data validation and translation issues.


## Installation

```
composer require majerome/magento2-module-disabletwofactorauth
bin/magento module:enable Majerome_PricesMassUpdate
bin/magento setup:upgrade
```

## Usage

- After installing this module, try the console command 
```bin/magento majerome:mass-update:prices```,
- 1st chose your language EN or FR,
- Then enter a target price to update the whole catalog.

![Demo](https://rawcdn.githack.com/majerome/magento2-module-pricesmassupdate/master/docs/demo.png)

## License

[MIT](https://opensource.org/licenses/MIT)
