<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;

use App\Models\Product;
use App\Models\FullProduct;
use App\Models\Like;
use App\Models\User;
use App\Models\Category;
use App\Helpers;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
      $this->auth = $auth;
    }

    public function index (Request $request) {
      $category_id = $request->get('category_id');
      $user_id = $request->get('user_id');
      $products = Product::where('status', '=', 'published')->orWhere('status', '=', 'accepted');

      if (!is_null($category_id)) {
        $category = Category::where("id", $category_id)->first();
        if (!is_null($category)) {
          $categories = DB::table('categories')->where("id", $category_id)->orWhere("parent_category_id", $category_id);
          $categoryIds = [];
          foreach ($categories->get() as $key => $cat) {
            array_push($categoryIds, $cat->id);
          }
          $products = $products->whereIn("category_id", $categoryIds);
        }
      }

      if (!is_null($user_id)) {
        $products = $products->where("user_id", $user_id);
      }

      $newProducts = [];
      foreach ($products->get() as $key => $product) {
        array_push($newProducts, $product);
      }
      return Helpers::generateResponse("Products Loaded.", $newProducts)->success;
    }

    public function store (Request $request) {
      $data = $request->all(
        'name',
        'download_url',
        'description',
        'thumbnail_url',
        'price',
        'category_id',
        'tags'
      );
      $validate = $this->validate($request, [
        "name"          => "required|string|min:5",
        "download_url"  => "required",
        "description"   => "required",
        "thumbnail_url" => "required",
        "price"         => "required|numeric"
      ]);
      try {
        $slug = Helpers::slugify($request->input('name'));
        $uuid = Uuid::generate(4)->string;
        $response = Product::create([
          "id"              => $uuid,
          "user_id"         => $this->auth->user()->id,
          "name"            => $data["name"],
          "download_url"    => $data["download_url"],
          "description"     => $data["description"],
          "thumbnail_url"   => $data["thumbnail_url"],
          "price"           => $data["price"],
          "download_times"  => 0,
          "likes"           => 0,
          "status"          => "pending",
          "slug"            => $slug,
          "category_id"     => $data['category_id'],
        ]);
        return Helpers::generateResponse("Project uploaded successfully and will be checked by our admin. Please wait for our confirmation.", $response)->success;
      } catch (\Exception $e) {
        return Helpers::generateResponse("Failed to create project.")->fail;
      }
    }
    public function show (Request $request, $product_id) {
      $product = Product::where("id", $product_id)->first();
      if (!is_null($product)) {
        return Helpers::generateResponse("Product found.", $product)->success;
      }
      return Helpers::generateResponse("Product not found.")->fail;
    }
    public function update (Request $request, $product_id) {
      $product = FullProduct::where("id", $product_id)->first();
      $data = $request->all();
      if (!is_null($product)) {
        if ($this->auth->user()->id === $product->user_id || $this->auth->user()->user_type > 1) {
          if (isset($data["name"])) {
            $data["slug"] = Helpers::slugify($request->input('name'));
          };
          $product->update($data);
          return Helpers::generateResponse("Product Updated.", $product)->success;
        }
      }
      return Helpers::generateResponse("Product not found.")->fail;
    }
    
    public function updateStatus (Request $request, $product_id, $status) {
      $product = Product::where("id", $product_id)->first();
      if (!is_null($product)) {
        if ($this->auth->user()->id === $product->user_id || $this->auth->user()->user_type > 1) {
          switch ($status) {
            case 'pending':
              $product->update([
                "status"  => 'pending'
              ]);
              break;

            case 'publish':
              if ($product->status === 'accepted' || $this->auth->user()->user_type > 1) {
                $product->update([
                  "status"  => 'published'
                ]);
              }
              break;

            case 'hide':
              if ($product->status === 'accepted' || $this->auth->user()->user_type > 1) {
                $product->update([
                  "status"  => 'draft'
                ]);
                break;
              }

            case 'accept':
              if ($this->auth->user()->user_type > 1) {
                $product->update([
                  "status"  => 'accepted'
                ]);
              }
              break;

            case 'reject':
              if ($this->auth->user()->user_type > 1) {
                $product->update([
                  "status"  => 'rejected'
                ]);
              }
              break;

            case 'ban':
              if ($this->auth->user()->user_type > 1) {
                $product->update([
                  "status"  => 'banned'
                ]);
              }
              break;
          }

          return Helpers::generateResponse("Successfully update product status.", $product)->success;
        }
        return Helpers::generateResponse("An error occured.")->fail;
      }
      return Helpers::generateResponse("Product not found.")->fail;
    }

    public function destroy (Request $request, $product_id) {
      $product = Product::where("id", $product_id)->get()->first();
      if (!is_null($product)) {
        if ($product->user_id === $this->auth->user()->id || $this->auth->user()->user_type > 1) {
          Product::destroy($product_id);
          return Helpers::generateResponse("Successfully delete product.")->success;
        }
        return Helpers::generateResponse("Failed to delete product.")->fail;
      }
      return Helpers::generateResponse("Product not found.")->fail;
    }
}
