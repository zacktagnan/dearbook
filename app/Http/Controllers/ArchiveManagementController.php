<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ArchiveManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('ArchiveManagement/Index', [
            'success' => session('success'),
        ]);
    }

    public function notifyProcessEnding(string $processType)
    {
        // $from = match ($processType) {
        //     'archive_all_selected_from_trash' => 'trash',
        // };
        $from = explode("from_", $processType)[1];

        return back()->with([
            'success' => [
                'from' => $from,
                'message' => 'Conjunto de publicaciones archivado satisfactoriamente.',
            ],
        ]);
    }
}
