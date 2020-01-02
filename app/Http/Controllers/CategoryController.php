<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Helpers;
use Webpatser\Uuid\Uuid;

class CategoryController extends Controller
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

    public function index (Request $request) {
      $categories = Category::where("parent_category_id", null)->get();
      foreach ($categories as $key => $category) {
        $category["subcategories"] = Category::where("parent_category_id", $category->id)->get()->toArray();
      }
      return Helpers::generateResponse("All Categories Loaded.", $categories)->success;
    }

    public function store (Request $request) {
      
      $validate = $this->validate($request, [
        "name"  => 'required|min:3'
      ]);
      try {
        $slug = Helpers::slugify($request->input('name'));
        if (is_null(Category::where("slug", $slug)->first())) {
          $uuid = Uuid::generate(4)->string;
          $category = [
            "id"    => $uuid,
            "name"  => $request->input('name'),
            "slug"  => $slug,
            "description" => $request->input('description'),
            "parent_category_id" => $request->input('parent_category_id'),
          ];
          $response = Category::create($category);
          return Helpers::generateResponse("Category added successfully.", $response)->success;
        } else {
          return Helpers::generateResponse("Category Exists.")->fail;
        }
      } catch (\Exception $e) {
        return Helpers::generateResponse("Failed to add category.")->fail;
      }
    }

    public function show (Request $request, $category_id) {
      if (!is_null($request->get("parent"))) {

      }
      $category = Category::where("id", $category_id)->first();
      if (!is_null($category)) {
        return Helpers::generateResponse("Category found.", $category)->success;
      }
      return Helpers::generateResponse("No Category found.")->fail;
    }

    public function update (Request $request, $category_id) {
      $category = Category::find($category_id);
      try {
        $category->slug = !is_null($request->input('name')) ? Helpers::slugify($request->input('name')) : $category->slug;
        $category->name = !is_null($request->input('name')) ? $request->input('name') : $category->name;
        $category->description = !is_null($request->input('description')) ? $request->input('description') : $category->description;
        $category->parent_category_id = !is_null($request->input('parent_category_id')) ? $request->input('parent_category_id') : $category->parent_category_id;
        
        $category->save();
        return Helpers::generateResponse("Category updated successfully.", $category)->success;
      } catch (\Exception $e) {
        return Helpers::generateResponse("Failed to update category.")->fail;
      }
    }

    public function destroy (Request $request, $category_id) {
      $category = Category::find($category_id);
      if (!is_null($category)) {
        if (is_null($category->parent_category_id)) {
          $subcategory = Category::where("parent_category_id", $category->id)->delete();
        }
        $response = Category::destroy($category_id);
        return Helpers::generateResponse("Successfully delete categories")->success;
      } else {
        return Helpers::generateResponse("Category not found.")->fail;
      }
    }
}