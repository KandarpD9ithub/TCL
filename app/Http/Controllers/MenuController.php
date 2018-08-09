<?php

namespace App\Http\Controllers;

use App\Category;
use App\Employee;
use App\Franchise;
use App\Http\Requests\MenuRequest;
use App\InactiveMenuItems;
use App\Menu;
use App\Product;
use App\ProductPrice;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::whereParentId(0)->get();
        if(!superAdmin()) {
            $franchiseId = Employee::where('user_id',\Auth::user()->id)->first()->franchise_id;
            $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
            $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        }
        return view('menu.index', compact('categories', 'productPrice', 'inactive'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::where('is_active', '1')->pluck('name', 'id')->toArray();
        $menuProductId  = Menu::pluck('product_id')->toArray();
        $product = Product::where('is_active', '1')->whereNotIn('id', $menuProductId)->where(function ($query) {
            $query->where('effective_from', null)->orWhere('effective_from', "<=", date("Y-m-d"));
        })
            ->where(function ($query) {
                $query->where('effective_to', null)->orWhere('effective_to', ">=", date("Y-m-d"));
            })
            ->pluck('name', 'id')->toArray();
        $parent = Category::where('is_active', '1')->select('id', 'name', 'parent_id')->get()->toArray();
        $parentCategory = [];
        if (!empty($parent)) {
            foreach ($parent as $value) {
                if ($value['parent_id'] =='0') {
                    $parentCategory[$value['id']]= $value['name'];
                } else {
                    if(array_key_exists($value['parent_id'], $category)) {
                        $parentCategory[$value['id']] = $category[$value['parent_id']] . '/' . $value['name'];
                    }
                }
            }
        }
        return view('menu.create', compact('category', 'product', 'parentCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MenuRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request)
    {
        try {
            $input = $request->all();
            $query = array_values($input['menu']);
            foreach ($query as $value) {
                $categoryId = $value['category_id'];
                if (isset($value['product_id'])) {
                    foreach ($value['product_id'] as $product) {
                        Menu::create([
                            'category_id' => $categoryId,
                            'product_id'  => $product
                        ]);
                    }
                } else {
                    return redirect()->back()->with('error', \Lang::get('messages.select_product'));
                }
            }
            return redirect()->route('menu.index')->with('success', \Lang::get('messages.added'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
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
        //
    }

    public function inactiveMenuItems($productId)
    {
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $menuId = Menu::where('product_id', $productId)->first()->id;
        $inactive = InactiveMenuItems::pluck('menu_id')->toArray();
        if (in_array($menuId, $inactive)) {
            $query = InactiveMenuItems::where('menu_id', $menuId)->delete();
        } else {
            $query = InactiveMenuItems::create([
                'franchise_id'  => $franchiseId,
                'menu_id'   => $menuId
            ]);
        }
        return redirect()->route('menu.index');
    }

    /**
     * @param $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeProducts($productId)
    {
        $menuId = Menu::where('product_id', $productId)->first()->id;
        $inactive = InactiveMenuItems::pluck('menu_id')->toArray();
        if (in_array($menuId, $inactive)) {
            InactiveMenuItems::where('menu_id', $menuId)->delete();
        }
        Menu::where('product_id', $productId)->delete();
        return redirect()->route('menu.index')->with('success', 'Product removed successfully');
    }
}
