<?php

return [
    'binary' => env('WKHTMLTOPDF_BINARY', '/usr/bin/wkhtmltopdf'),
    'options' => [
        'enable-local-file-access' => true,
    ],
];
