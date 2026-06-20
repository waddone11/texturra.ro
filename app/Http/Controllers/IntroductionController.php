<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IntroductionController extends Controller
{
    /**
     * Display the introduction page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('introduction');
    }
}
