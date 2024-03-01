<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class cercador extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $selectedFiltro, public string $searchTerm, public array $categories, public string $selectedCategoria)
    {
        $this->selectedFiltro = $selectedFiltro;
        $this->searchTerm = $searchTerm;
        $this->categories = $categories;
        $this->selectedCategoria = $selectedCategoria;
    }
    public function render()
    {
        return view('components.cercador');
    }
}
