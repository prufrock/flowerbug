<?php

namespace App\Http\Controllers;

use App\Domain\Project;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $projects = (resolve(Project::class))->all();
        return view('home', ["projects" => $projects]);
    }
}
