<?php

return [
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'KraftX')),
    'default_title' => env('SEO_DEFAULT_TITLE', env('APP_NAME', 'KraftX')),
    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Discover handcrafted decor, spiritual wall art, and thoughtful home accents from KraftX.'
    ),
    'default_image' => env('SEO_DEFAULT_IMAGE', 'assets/images/logo/logo.png'),
    'support_phone' => env('SEO_SUPPORT_PHONE', '+91 99900 10933'),
    'support_email' => env('SEO_SUPPORT_EMAIL', 'thekraftx@gmail.com'),
    'address' => env('SEO_ADDRESS', 'Gaur City Center, Gaur City West, Greater Noida, UP, India, 201308'),
    'country_code' => env('SEO_COUNTRY_CODE', 'IN'),
    'social' => [
        'facebook' => env('SEO_FACEBOOK_URL', ''),
        'instagram' => env('SEO_INSTAGRAM_URL', ''),
        'x' => env('SEO_X_URL', ''),
        'youtube' => env('SEO_YOUTUBE_URL', ''),
    ],
];
