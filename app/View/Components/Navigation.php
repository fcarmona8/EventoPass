<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Navigation extends Component
{
    public $activeRoute;

    public function __construct($activeRoute)
    {
        $this->activeRoute = $activeRoute;
    }

    public function render(): View
    {
        return view('components.navigation');
    }
}
