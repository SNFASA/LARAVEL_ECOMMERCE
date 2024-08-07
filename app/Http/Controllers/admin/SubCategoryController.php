<?php

namespace App\Http\Controllers\admin;
use App\Models\Subcategory;
use App\Models\category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $subcategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
            ->latest('sub_categories.id')->leftJoin('categories','categories.id','sub_categories.category_id');

        if (!empty($keyword)) {
            $subcategories = $subcategories->where('sub_categories.name', 'like', '%' . $keyword . '%');
            $subcategories = $subcategories->orwhere('categories.name', 'like', '%' . $keyword . '%');
        }

        $subcategories = $subcategories->paginate(10); // paginate the results with 10 items per page

        return view('admin.sub_category.list', compact('subcategories'));
    }
    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category_id' => 'required',
            'status' => 'required',
        ]);
    
        if ($validator->passes()) {
            $subCategory = new Subcategory(); // Correct model instantiation
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category_id; // Ensure correct field
            $subCategory->save();
    
            $request->session()->flash('success', 'Sub Category added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category added successfully'
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
}


