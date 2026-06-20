<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TooltipCta extends Component
{
    public $links;
    public $title;
    public $type;

    public function __construct($links = [], $title = null, $type = 'button')
    {
        $this->links = $links;
        $this->title = $title;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.tooltip-cta');
    }
}
