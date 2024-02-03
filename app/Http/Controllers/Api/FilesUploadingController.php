<?php

namespace App\Http\Controllers\Api;

use App\Models\Empresa;
use App\Models\FileEntry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FilesUploadingController extends Controller
{
    public function uploadCompanyFile(Request $request) {
        $file = $request->file;
        $filename = date('mdYHis') . uniqid() .'.'. File::extension($file->getClientOriginalName());
        $filepath = 'company/';

        if(Storage::disk('uploads')->put($filepath.$filename,  File::get($file))){
            $file_entry = FileEntry::create([
                'filename' => $filename,
                'path'     => "/storage/files/uploads/".$filepath,
                'mime'     => $file->getMimeType(),
                'size'     => $file->getSize(),
            ]);

            $data = Empresa::find($request->id);
            $data->id_file = $file_entry->id;
            $data->save();

            return response()->json(['success' => true], 200);
        }

        return response()->json(['success' => false], 500);
    }
}
