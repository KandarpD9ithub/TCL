<?php

/**
 * @package App/Http/Controllers
 *
 * @class ProductController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 * @author Bhavana <bhavana@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;
use App\Http\Requests\ProductRequest;
use App\Tag;
use Illuminate\Support\Facades\DB;
use App\Menu;
use Image;
use App\Product;
use App\ProductTag;
use App\Category;
use App\Tax;
use App\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where(function ($query) {
            $query->where('effective_from', NULL)->orWhere('effective_from', "<=", date("Y-m-d"));
        })->where(function ($query) {
            $query->where('effective_to', NULL)->orWhere('effective_to', ">=", date("Y-m-d"));
        })->paginate(10);

        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $parent = Category::pluck('id','name','parent_id')->toArray();
        $taxes= Tax::whereIsActive(1)->where('tax_type', '!=', '2')
            ->pluck('tax_name', 'id')->toArray();
        $parentCategory = Category::pluck('name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();
        return view('product.create', compact('categories', 'parent','taxes','parentCategory', 'tags'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        $input = $request->all();
        try {
            $product = Product::create($input);
            $tagIds = $input['tag_id'];
            foreach ($tagIds as $tagId){
                ProductTag::create([
                    'product_id' => $product->id,
                    'tag_id'      => $tagId
                ]);
            }
            /** for adding and storing images */
            if (Input::hasFile('image')) {
                $filename = Input::file('image')->getRealPath();
                $ext      = Input::file('image')->getClientOriginalExtension();
                $imageName = Storage::putFile('', new File($filename));
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'file_name' =>  $imageName,
                    'original_file_name' => Input::file('image')->getClientOriginalName()
                ]);
                    $files = explode('.', $imageName);
                    $input['image'] = $files[0].'_1.'.$ext;
                    $destinationPath = public_path('upload');
                    $img = Image::make($filename);
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$input['image']);
            }
            return redirect()->route('product.index')->with('success', \Lang::get('messages.added'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error',$error->getMessage());
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $productId
     * @return \Illuminate\Http\Response
     */
    public function edit($productId)
    {
        $product = Product::findOrFail($productId);
        $categories = Category::pluck('name', 'id')->toArray();
        $parent = Category::pluck('id','name','parent_id')->toArray();
        $taxes= Tax::whereIsActive(1)->where('tax_type', '!=', '2')
            ->pluck('tax_name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();
        $productTags = ProductTag::whereProductId($productId)->pluck('tag_id')->toArray();
        $productsPhoto = ProductPhoto::whereProductId($productId)->first();
        return view('product.edit', compact('product','categories', 'parent','taxes', 'tags', 'productTags', 'productsPhoto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProductRequest $request
     * @param  int $productId
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $productId)
    {
        $input = $request->all();
        $product = Product::findOrFail($productId);
        $product_code = Product::whereId($productId)->first()->product_code;
        try {
            if (isset($input['is_active'])) {
                $input['is_active'] = 1;
            } else {
                $input['is_active'] = 0;
            }
            if(isset($input['tag_id'])) {
                ProductTag::whereproductId($product->id)->delete();
                $tagIds = $input['tag_id'];
                foreach ($tagIds as $tagId) {
                    ProductTag::create([
                        'product_id' => $product->id,
                        'tag_id'      => $tagId
                    ]);
                }
            } else {
                return redirect()->back()->with('error', \Lang::get('messages.default_must'));
            }
            if (empty($input['effective_from'])) {
                // If date is not specified only status of product will be changed
                Product::findOrFail($productId)->update([
                    'tax_id'       => $input['tax_id'],
                    'is_active'    => $input['is_active'],
                    'price'        => $input['price'],
                    'name'         => $input['name'],
                    'description'  => $input['description'],
                    'product_code' => $input['product_code']
                ]);
            } else {
                // If date is specified
                $effectiveFrom = Product::whereId($productId)->first()->effective_from;
                $effectiveTo = Product::whereId($productId)->first()->effective_to;
                if ($effectiveFrom == null && $effectiveTo != null || $effectiveFrom != null) {
                    $productCode = Product::whereId($productId)->first()->product_code;
                    $idOfSecondRecord = Product::where('id', '!=', $productId)->where('product_code', $productCode)->first()->id;
                    $from = Product::whereId($idOfSecondRecord)->first()->effective_from;
                    if ($from != null) {
                        $productCode = Product::whereId($productId)->first()->product_code;
                        $allRows = Product::where('product_code', $productCode)->get()->toArray();
                        foreach ($allRows as $row) {
                            if ($row['effective_from'] != null) {
                                $update_id = $row['id'];
                            }
                        }
                        Product::where('id', $update_id)->update(['effective_from' => date("Y-m-d", strtotime($input['effective_from'])), 'price' => $input['price']]);
                        Product::where('id', $productId)->update(['effective_to' => date("Y-m-d", strtotime($input['effective_from']) - (60 * 60 * 24))]);
                    } else {
                        Product::where('id', $productId)->update(['effective_from' => date("Y-m-d", strtotime($input['effective_from'])), 'price' => $input['price']]);
                        Product::where('id', $idOfSecondRecord)->update(['effective_to' => date("Y-m-d", strtotime($input['effective_from']) - (60 * 60 * 24))]);
                    }
                }
                if ($effectiveFrom == null && $effectiveTo == null) {
                    $from = date("Y-m-d", strtotime($input['effective_from']) - (60 * 60 * 24));
                    Product::findOrFail($productId)->update(['effective_to' => $from, 'price' => $input['price']]);
                    $input['product_code'] = $product_code;
                    $input['effective_from'] = date("Y-m-d", strtotime($input['effective_from']));
                    Product::create($input);
                }
            }

            if (Input::hasFile('image')) {
                $filename = Input::file('image')->getRealPath();
                $ext      = Input::file('image')->getClientOriginalExtension();
                $imageName = Storage::putFile('', new File($filename));
                $productsPhoto = ProductPhoto::whereProductId($productId)->first();
                if ($productsPhoto) {
                    $productsPhoto->update([
                        'file_name' =>$imageName,
                        'original_file_name' => Input::file('image')->getClientOriginalName()
                    ]);
                } else {
                    ProductPhoto::create([
                        'product_id' => $productId,
                        'file_name' =>  $imageName,
                        'original_file_name' => Input::file('image')->getClientOriginalName()
                    ]);
                }

                $files = explode('.', $imageName);
                $input['image'] = $files[0].'_1.'.$ext;
                $destinationPath = public_path('upload');
                $img = Image::make($filename);
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$input['image']);
            }

            return redirect()->route('product.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return back()->with('error',  $error->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
