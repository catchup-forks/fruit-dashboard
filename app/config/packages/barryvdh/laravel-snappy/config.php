<?php

return array(
    'pdf' => array(
        'enabled' => true,
        'binary' => $_ENV['WKHTMLTOPDF_BINARY_LOCATION'],
        'timeout' => false,
        'options' => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => $_ENV['WKHTMLTOIMAGE_BINARY_LOCATION'],
        'timeout' => false,
        'options' => array(),
    ),
);
