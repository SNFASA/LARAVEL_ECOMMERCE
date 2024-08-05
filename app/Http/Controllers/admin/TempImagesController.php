<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('temp-images', 'public'); // Ensure 'public' is specified for storage

            $tempImage = new TempImage();
            $tempImage->path = $path;
            $tempImage->name = $image->getClientOriginalName(); // Add this line if you need the original name
            $tempImage->save();

            return response()->json(['success' => 'Image uploaded successfully', 'image_id' => $tempImage->id]);
        }

        return response()->json(['error' => 'No image uploaded']);
    }
}
