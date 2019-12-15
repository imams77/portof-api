<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class UploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function image(Request $request) {
      $validator = $this->validate($request, [
        'image'       => 'required|image|mimes:jpg,jpeg,JPG,JPEG,gif,GIF,png,PNG|max:1500'
      ]); 
      try {
        $response = Storage::disk('do')->putFile('images', $request->file('image'), 'public');
        return response()->json([
          "success"   => true,
          "data"      => [
            "url"     => env('DO_FILE_URL').'/'.$response
          ],
          "message"   => "Image uploaded Successfully."
        ], 200);
      } catch (\Exception $e) {
        return response()->json(['message' => 'Upload Image Failed!'], 400);
    }
  }
  public function file(Request $request) {
    $validator = $this->validate($request, [
      'file'       => 'required|file|mimes:rar,zip|max:150000|'
    ]); 
    try {
      $response = Storage::disk('do')->putFile('files', $request->file('file'), 'public');
      return response()->json([
        "success"   => true,
        "data"      => [
          "url"     => env('DO_FILE_URL').'/'.$response
        ],
        "message"   => "File uploaded Successfully."
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Upload Image Failed!'], 400);
    }
  }
}
