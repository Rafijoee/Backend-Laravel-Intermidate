<?php

return [
    // Config for ImageKit
    'imagekit' => [
        'url' => env('IMAGEKIT_URL_ENDPOINT',),
        'public_key' => env('IMAGEKIT_PUBLIC_KEY'),
        'private_key' => env('IMAGEKIT_PRIVATE_KEY'),
    ],
];