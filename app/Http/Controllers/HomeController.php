<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $projects = json_decode(Storage::disk('local')->get('projects.json'), true);
        return view('home', $projects);
    }
}
