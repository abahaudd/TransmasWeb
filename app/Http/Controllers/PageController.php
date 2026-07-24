<?php

namespace App\Http\Controllers;

use App\Services\CmsService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly CmsService $cms) {}

    public function home(): View
    {
        $page = $this->cms->page('home');

        if ($page === null) {
            return view('welcome');
        }

        return view($this->cms->templateView($page), [
            'page' => $page,
        ]);        
        
        // return $this->render('home');
    }

    public function show(string $slug): View
    {
        return $this->render($slug);
    }

    protected function render(string $slug): View
    {
        $page = $this->cms->page($slug);

        if ($page === null) {
            return view('welcome');
        }

        return view($this->cms->templateView($page), [
            'page' => $page,
        ]);
    }
}
