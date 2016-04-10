<?php

return [

    // For 'Route::group()' call to declare controller action.
    'route' => 
    [
        'middleware' => ['web'],
    ],
    
    // Directories to search requesting files.
    'directories' => 
    [
        public_path(),
        public_path() . "/uploads/",
    ],

    // Directory (temporary) to store generated preview.
    'store' => public_path() . "/previews/",
    
    // Allowed resolutions and modes (to avoid maleficent requests)
    'allowed' =>
    [
        // If empty - all allowed
        // [300, 200, 'scale'],
        // [640, 420, 'fitin'],
    ],
    
    'max' =>
    [
        // Maximum requesting width.
        'width'  => 1600,

        // Maximum requesting heigth.
        'heigth' => 1200,
    ],
    
    // Background RGB color in fitin/fitout modes.
    'fill_color' => "ffffff",
    
    // Image to return if can't find requesting one.
    'no_image_path' => public_path() . '/img/get_img_fail.png',
    
    'watermark' => 
    [
        //@todo
    ],
    
    // Headers for returning files.
    'headers' =>
    [
        'Cache-Control' => 'public, max-age=2592000',
    ],
];
