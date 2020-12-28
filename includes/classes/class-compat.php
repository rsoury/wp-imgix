<?php

namespace WPImgIX;

defined('ABSPATH') || exit;

class Compat
{
  public function __construct()
  {
    // Rank Math
    add_filter('rank_math/opengraph/facebook/og_image', array($this, 'wrap_image'));
    add_filter('rank_math/opengraph/facebook/og_image_secure_url', array($this, 'wrap_image'));
    add_filter('rank_math/opengraph/twitter/twitter_image', array($this, 'wrap_image'));
  }

  public function wrap_image($image_url)
  {
    if (function_exists('imgix_url')) {
      $image_pathname = parse_url($image_url, PHP_URL_PATH);
      $image_url = \WPImgIX::instance()->build_url($image_pathname, [
        'auto' => 'format,compress'
      ]);
      return $image_url;
    }

    return $image_url;
  }
}

new Compat();
