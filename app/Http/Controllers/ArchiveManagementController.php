<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ArchiveManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('ArchiveManagement/Index');
    }
}
