<?php

namespace App\Http\Controllers;

use App\Category;
use App\Employee;
use App\InactiveMenuItems;
use App\Menu;
use App\Product;
use App\ProductPrice;
use App\SpecialProduct;
use Illuminate\Http\Request;

class SpecialProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::whereIsActive(1)->pluck('name', 'id')->toArray();
        $productsPrice=[];
        $franchiseId = Employee::where('user_id',\Auth::user()->id)->first()->franchise_id;
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        $categories = Category::whereIsActive(1)->pluck('id')->toArray();
        $menuProductsId = Menu::whereNotIn('id', $inactive)->whereIn('category_id', $categories)
            ->pluck('product_id')->toArray();
        $specialProducts = SpecialProduct::where('franchise_id', $franchiseId)
            ->join('products', 'products.id', '=', 'special_products.product_id')
            ->where('products.is_active', 1)
            ->whereIn('products.id', $menuProductsId)
            ->select(['special_products.id', 'special_products.product_id', 'products.price', 'products.product_code'])->get();
        $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
        $franchiseProductPrice = $franchiseProductId = $menu = [];
        if (!empty($productPrice)) {
            foreach ($productPrice as $price) {
                $franchiseProductId[$price['product_id']] = $price['price'];
                $franchiseProductPrice[] = $price['product_id'];
            }
        }
            foreach($specialProducts as $key => $value)
            {
                if (in_array($specialProducts[$key]['product_id'], $franchiseProductPrice)) {
                    $price = $franchiseProductId[$value['product_id']];
                } else {
                    $price = Product::where('id', $value['product_id'])->first()->price;
                }

                $productsPrice[] = [
                    'id' => $value['id'],
                    'product_id' => $value['product_id'],
                    'name' => $products[$value['product_id']],
                    'price' => $price,
                    'product_code' => Product::where('id',$value['product_id'])->first()->product_code
                ];
            }
       return view('specialProduct.index', compact('productsPrice'));


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
        $products = Product::whereIsActive(1)->whereIn('id', $menuProductsId)->pluck('name', 'id')->toArray();
        return view('specialProduct.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
            foreach($input['product'] as $value)
            {
                $exists = SpecialProduct::where('franchise_id', $franchiseId)->where('product_id', $value)->get()->toArray();
                if (empty($exists)) {
                    SpecialProduct::create([
                        'product_id' => $value,
                        'franchise_id' => $franchiseId
                    ]);
                } else{
                    return redirect()->back()->with('error', \Lang::get('views.product_exists'));
                }

            }
            return redirect()->route('special-product.index')->with('success', \Lang::get('messages.added'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error', $error->getMessage());
        }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            SpecialProduct::findOrfail($id)->delete();
            return redirect()->back()->with('success', \Lang::get('messages.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
        }    }
}
