[WP ImgIX](https://github.com/rsoury/wp-imgix) is a Wordpress Plugin to automatically load all your existing (and future) WordPress images via the imgix service for smaller, faster, and better looking images.

This plugin handles modifying WordPress image URLs to use the ImgIX service.
It works very well with [S3-Uploads](https://github.com/humanmade/S3-Uploads)

## Installation

1. Upload and enable this plugin.
2. Add `define( 'WP_IMGIX_URL', 'your.imgix.net' )` to your `wp-config.php` file.
2. If you're signing urls, Add `define( 'WP_IMGIX_SIGNING_TOKEN', 'abcdefg123456' )` to your `wp-config.php` file.

## Usage

Typically the above steps are all you need to do however you can use the following public facing functions and filters.

### Functions

#### `imgix_url( string $image_url, array $args = [] )`

This function returns the ImgIX URL for a given image.

```php
$image_url = 'https://my-bucket.s3.us-east-1.amazonaws.com/path/to/image.jpg';
$args      = [
	'x'  => '300'
	'y' => '300',
	'fit' => 'crop'
];

$url = imgix_url( $image_url, $args );
```

### Filters

The following filters allow you to modify the output and behaviour of the plugin. The filters below can be added to your theme's `functions.php` to modify the behavior of your imgix URLs.

#### `imgix_disable_in_admin`

Defaults to `true`.

```php
add_filter( 'imgix_disable_in_admin', '__return_false' );
```

#### `imgix_override_image_downsize`

Defaults to `false`. Provides a way of preventing ImgIX from being applied to images retrieved from WordPress Core at the lowest level, you might use this if you wanted to use `imgix_url()` manually in specific cases.

```php
add_filter( 'imgix_override_image_downsize', '__return_true' );
```

#### `imgix_skip_for_url`

Allows skipping the ImgIX URL for a given image URL. Defaults to `false`.

```php
add_filter( 'imgix_skip_for_url', function ( $skip, $image_url, $args ) {
	if ( strpos( $image_url, 'original' ) !== false ) {
		return true;
	}

	return $skip;
}, 10, 3 );
```

#### `imgix_pre_image_url`

Filters the ImgIX image URL excluding the query string arguments.

```php
add_filter( 'imgix_pre_image_url', function ( $image_url, $args ) {
	if ( rand( 1, 2 ) === 2 ) {
		$image_url = str_replace( WP_IMGIX_URL, WP_IMGIX_URL_2, $image_url );
	}

	return $image_url;
}, 10, 2 );
```

#### `imgix_pre_args`

Filters the query string parameters appended to the imgix image URL.

```php
add_filter( 'imgix_pre_args', function ( $args ) {
	if ( isset( $args['fit'] ) ) {
		$args['fill'] = 'blur';
	}

	return $args;
} );
```

#### `imgix_remove_size_attributes`

Defaults to `true`. `width` & `height` attributes on image tags are removed by default to prevent aspect ratio distortion that can happen in some unusual cases where `srcset` sizes have different aspect ratios.


```php
add_filter( 'imgix_remove_size_attributes', '__return_true' );
```

## Credits
Created by Web Doodle

Forked from [HumanMade's Tachyon Plugin](https://github.com/humanmade/tachyon-plugin) -- Special thanks to the HumanMade team for working on accelerated Wordpress projects.

Written and maintained by [Ryan Soury](https://github.com/rsoury).
