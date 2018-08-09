<?php

namespace App\Http\Controllers;

use App\Category;
use App\Employee;
use App\Http\Requests\ProductPriceRequest;
use App\InactiveMenuItems;
use App\Menu;
use App\Product;
use App\ProductPrice;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $productPrice = ProductPrice::whereFranchiseId($franchiseId)->get()->toArray();
        $categories = Category::whereIsActive(1)->pluck('id')->toArray();
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        $menuProductsId = Menu::whereNotIn('id', $inactive)->whereIn('category_id', $categories)
            ->pluck('product_id')->toArray();
        $product = Product::whereIsActive(1)->whereIn('id', $menuProductsId)->where(function ($query) {
            $query->where('effective_from', null)->orWhere('effective_from', "<=", date("Y-m-d"));
        })->where(function ($query) {
            $query->where('effective_to', null)->orWhere('effective_to', ">=", date("Y-m-d"));
        })->pluck('name', 'id')->toArray();

        if (empty($productPrice)) {
            $data='';
        } else {
            foreach ($productPrice as $key => $d) {
                if (!empty($product[$d['product_id']])) {
                    if (in_array($product[$d['product_id']], $product)) {
                        $data[] = $d;
                    } else {
                        $data[] ="";
                    }
                }
            }
        }
        return view('productPrice.index', compact('data', 'product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        $categories = Category::whereIsActive(1)->pluck('id')->toArray();
        $menuProductsId = Menu::whereNotIn('id', $inactive)->whereIn('category_id', $categories)
            ->pluck('product_id')->toArray();
        $products = Product::whereIsActive(1)->whereIn('id', $menuProductsId)->where(function($query) {
            $query->where('effective_from', NULL)->orWhere('effective_from' ,"<=", date("Y-m-d"));
        })->where(function ($query) {
            $query->where('effective_to', NULL)->orWhere('effective_to',">=", date("Y-m-d"));
        })->pluck('name', 'id')->toArray();
        
        foreach ($products as $key => $value) {
            $checkProduct = ProductPrice::where('product_id',$key)->exists();
            if ($checkProduct) {
                unset($products[$key]);
            }
        }
        return view('productPrice.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProductPriceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductPriceRequest $request)
    {
        $input = $request->all();
        $employee = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        try {
            $productPrice = array_values($input['product_price']);
            if (count($input['product_price']) > 0) {
                foreach ($productPrice as $price) {
                    ProductPrice::create([
                        'product_id' => $price['product_id'],
                        'price'     => $price['price'],
                        'franchise_id' => $employee
                    ]);
                }
            }
        } catch (\Exception $error) {
            return redirect()->back()->with('error', $error->getMessage());
        }
        return redirect()->route('product-price.index')->with('success', \Lang::get('messages.added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $price = ProductPrice::findOrFail($id);
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        $categories = Category::whereIsActive(1)->pluck('id')->toArray();
        $menuProductsId = Menu::whereNotIn('id', $inactive)->whereIn('category_id', $categories)
            ->pluck('product_id')->toArray();
        $products = Product::whereIsActive(1)->whereIn('id', $menuProductsId)->where(function($query) {
            $query->where('effective_from', NULL)->orWhere('effective_from' ,"<=", date("Y-m-d"));
        })->where(function ($query) {
            $query->where('effective_to', NULL)->orWhere('effective_to',">=", date("Y-m-d"));
        })->pluck('name', 'id')->toArray();
        return view('productPrice.edit', compact('price', 'products'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {        
        try {
            $input = $request->all();
            ProductPrice::findOrFail($id)->update($input);
            return redirect()->route('product-price.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return back()->with('error',  \Lang::get('messages.internal_error'));

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
