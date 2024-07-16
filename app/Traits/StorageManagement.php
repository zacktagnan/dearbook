<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

trait StorageManagement
{
    public function downloadAttachment(Attachment $attachment)
    {
        // return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
        // o, sino,
        return response()->download(storage_path('app/public/' . $attachment->path), $attachment->name);
    }

    private function deleteAlreadyUploadedFiles(array $allFilePaths): void
    {
        foreach ($allFilePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function deleteFolderIfEmpty(string $folder): void
    {
        /*
        $fileSystem = new Filesystem();
        $directory = storage_path('app/public') . '/' . $folder;
        // if ($fileSystem->exists($directory)) {
        // if (Storage::disk('public')->exists($folder)) {
        //     dd('directory [storage_path() . $folder]', $directory, 'SI EXISTE');
        //     // $fileSystem->deleteDirectory($directory);
        //     // Storage::deleteDirectory($directory);
        // } else {
        //     dd('directory [storage_path() . $folder]', $directory, 'NO EXISTE');
        // }

        $dirExists = Storage::disk('public')->exists($folder);
        // Esto(s) no funciona(n)
        // $isEmpty = Storage::files($directory);
        // $isEmpty = empty($fileSystem->files($directory));
        // Esto si
        $isEmpty = (count(glob("$directory/*")) === 0) ? 'Empty' : 'Not empty';

        dd('directory [storage_path() . $folder]', $directory, 'EXISTE o NO', $dirExists, 'VACÍO o NO', $isEmpty);
        */

        $directory = storage_path('app/public') . '/' . $folder;
        if (Storage::disk('public')->exists($folder) && count(glob("$directory/*")) === 0) {
            Storage::disk('public')->deleteDirectory($folder);
        }
    }
}
