# Laravel-PubPreview Beta

Component get resized images from public (storage) directory. 

You upload image and use it for preview:

``` html
<img src="{{ asset('img/photo1.jpg') }}" alt="Preview">
<img src="{{ asset($article->preview) }}" alt="Preview">
```

But usually image is too big so you have to generate smaller files:

``` html
<img src="{{ asset('img/photo1_100x100.jpg') }}" alt="Preview">
<img src="{{ asset($article->preview_100x100) }}" alt="Preview">
```

This library lets you to simplify it and make it on fly:

``` html
<img src="{{ pub_review('img/photo1.jpg',  100, 100, "scale") }}" alt="Preview">
<img src="{{ pub_review($article->preview, 100, 100, "scale") }}" alt="Preview">
```

Also you can configure image that must be shown if requested image doesn't exists.

@todo watermark

## How it works

Algotitm:

* `pub_review()` generates url to library controller action.
* Controller checks if it has resized image. If no - it generates resized image and saves it to temporaty directory.
* Controller returns resized image from temporary directory.

## Parameters of pub_review()

* Relative path to image file.
* Require width  in pixels.
* Require height in pixels.
* Required resize mode.

Available modes are 'scale', 'fitin', 'fitout':

![alt tag](http://zmicron.org/tpl/components/get_img/mode.png)

## Installation

Via Composer

``` bash
$ composer require masteryuri/laravel-pubpreview
```

Add service provider into `/config/app.php`:

```
'MasterYuri\PubPreview\ServiceProvider',
```

Publish config and resources:

```
php artisan vendor:publish --provider="MasterYuri\PubPreview\ServiceProvider"
```

## Configuration

Configuration file is 'pub-preview.php'. 
It has comments that describe all parameters.

[Read configuration file](config/pub-preview.php)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
