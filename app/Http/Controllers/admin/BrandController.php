<?php

namespace App\Http\Controllers\admin;
use App\Models\brand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class BrandController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $brands = brand::latest();

        if (!empty($keyword)) {
            $brands = $brands->where('name', 'like', '%' . $keyword . '%');
        }

        $brands = $brands->paginate(10); // paginate the results with 10 items per page

        return view('admin.brand.list', compact('brands'));
    }
    public function create()
    {
        return view('admin.brand.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required'
        ]);
    
        if ($validator->passes()) {
            $brand = new brand(); // Correct model instantiation
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
    
            $request->session()->flash('success', 'Brand added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
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
    public function edit($brandId, Request $request)
    {
        $brand = brand::find($brandId);
        if(empty($brand)){
            return redirect()->route('brands.index');
        }
        return view('admin.brand.edit',compact('brand'));
    }
    public function update($id, Request $request)
{
    $brand = Brand::find($id);
    if (empty($brand)) {
        return response()->json([
            'status' => false,
            'notFound' => true,
            'message' => 'Brand not found'
        ]);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,' . $brand->id,
        'status' => 'required|integer',  // Ensure status is validated as an integer
    ]);

    if ($validator->passes()) {
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status; // This should now receive the correct integer value
        $brand->save();

        $request->session()->flash('success', 'Brand updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully'
        ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}

        

    public function destroy($id, Request $request)
    {
        $brand = brand::find($id);
        if (empty($brand)) {
            $request->session()->flash('error', 'Brand not found');
            return response()->json([
                'status' => false,
                'message' => 'brand not deleted successfully'
            ]);
        }
        $brand->delete();
        $request->session()->flash('success', 'Brand deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }
}
