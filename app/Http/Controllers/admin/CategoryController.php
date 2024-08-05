<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image; // Import the Image class

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $categories = Category::latest();

        if (!empty($keyword)) {
            $categories = $categories->where('name', 'like', '%' . $keyword . '%');
        }

        $categories = $categories->paginate(10); // paginate the results with 10 items per page

        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
            'status' => 'required',
        ]);
    
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
    
            if (!empty($request->image_id)) {
                \Log::info('Image ID: ' . $request->image_id);
                $tempImage = TempImage::find($request->image_id);
                \Log::info('Temp Image: ' . $tempImage);
    
                if ($tempImage) {
                    $extArry = explode('.', $tempImage->path);
                    $ext = last($extArry);
                    $newImageName = $category->id . '.' . $ext;
                    $sPath = public_path() . '/temp/' . $tempImage->path;
                    $dPath = public_path() . '/storage/temp-images/' . $newImageName;
    
                    if (File::exists($sPath)) {
                        \Log::info('Source Path: ' . $sPath);
                        File::copy($sPath, $dPath);
                        \Log::info('File copied to: ' . $dPath);
    
                        $thumbPath = public_path() . '/storage/temp-images/thumb/' . $newImageName;
                        $img = Image::make($sPath);
                        //$img->resize(450, 600);
                        $img->fit(450, 600,function($constraint){
                            $constraint->upsize();
                        });
                        $img->save($thumbPath);
    
                        $category->image = $newImageName;
                        $category->save();
                    } else {
                        \Log::error('Source file does not exist: ' . $sPath);
                    }
                } else {
                    \Log::error('TempImage not found');
                }
            }
    
            $request->session()->flash('success', 'Category added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    

    public function getSlug(Request $request)
    {
        $slug = \Str::slug($request->title);
        return response()->json(['slug' => $slug]);
    }

    public function edit($categoryId, Request $request )
    {
        
        $category = Category::find($categoryId);
        if(empty($category)){
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId , Request $request)
    {
        $category = Category::find($categoryId);
        if(empty($category)){
            return response()->json([
                'satus' => false,
                'notFound' => true ,
                'message' => 'Category not found'
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
            'status' => 'required',
        ]);
    
        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $oldImage = $category->image;
    
            if (!empty($request->image_id)) {
                \Log::info('Image ID: ' . $request->image_id);
                $tempImage = TempImage::find($request->image_id);
                \Log::info('Temp Image: ' . $tempImage);
    
                if ($tempImage) {
                    $extArry = explode('.', $tempImage->path);
                    $ext = last($extArry);
                    $newImageName = $category->id .'-' .time() . $ext;
                    $sPath = public_path() . '/temp/' . $tempImage->path;
                    $dPath = public_path() . '\storage\temp-images' . $newImageName;
    
                    if (File::exists($sPath)) {
                        \Log::info('Source Path: ' . $sPath);
                        File::copy($sPath, $dPath);
                        \Log::info('File copied to: ' . $dPath);
    
                        $thumbPath = public_path() . '\storage\temp-images' . $newImageName;
                        $img = Image::make($sPath);
                        $img->fit(450, 600,function($constraint){
                            $constraint->upsize();
                        });
                        $img->save($thumbPath);
    
                        $category->image = $newImageName;
                        $category->save();

                        //delete old image hare 
                        FIle::delete(public_path().'\storage\temp-images'.$oldImage);
                        FIle::delete(public_path().'\storage'.$oldImage);

                    } else {
                        \Log::error('Source file does not exist: ' . $sPath);
                    }
                } else {
                    \Log::error('TempImage not found');
                }
            }
    
            $request->session()->flash('success', 'Category updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($categoryId , Request $request)
    {
        $category = Category::find($categoryId);
        if(empty($category)) {
            $request -> session()->flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category  not deleted successfully'
            ]);
            //return redirect()->route('categories.index'); 
        }
        FIle::delete(public_path().'\storage\temp-images'.$category->image);
        FIle::delete(public_path().'\storage'.$category->image);
        $category->delete();
        $request->session()->flash('success','Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);

    }
}
