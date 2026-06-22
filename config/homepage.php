<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Homepage Version
    |--------------------------------------------------------------------------
    |
    | Controls which homepage blade the "/" route renders:
    |   'old' => resources/views/home-old.blade.php (current, live design)
    |   'new' => resources/views/home-new.blade.php (redesign in progress)
    |
    | Default is 'old' so visitors keep seeing the existing homepage until the
    | redesign is finished and we flip HOMEPAGE_VERSION=new in the environment.
    | The /home-preview route always renders the 'new' blade regardless of this.
    |
    */

    'version' => env('HOMEPAGE_VERSION', 'old'),

];
