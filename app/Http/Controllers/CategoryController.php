<?php
/**
 * @package App/Http/Controllers
 *
 * @class CategoryController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com> Bhavana <bhavana@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App\Http\Controllers;

use App\Category;
use App\Tax;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::paginate(10);
        $parentCategory = Category::pluck('name', 'id')->toArray();
        $taxes= Tax::whereIsActive(1)->where('tax_type', '!=', '2')
            ->pluck('tax_name', 'id')->toArray();
        return view('category.index', compact('categories', 'parentCategory', 'taxes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $parent = Category::select('id','name','parent_id')->get()->toArray();
        $taxes= Tax::whereIsActive(1)->where('tax_type', '!=', '2')
            ->pluck('tax_name', 'id')->toArray();
        if (empty($parent)) {
            $parentCategory=[];
        } else {
            foreach($parent as $p => $value)
            {
                if($value['parent_id'] =='0') {
                    $parentCategory[$value['id']]= $value['name'];
                } else{
                    $parentCategory[$value['id']] = $categories[$value['parent_id']].'/'. $value['name'];
                }
            }
        }
        return view('category.create', compact('categories', 'parentCategory', 'taxes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $input = $request->all();
        try {

            Category::create([
                'tax_id'     => $input['tax_id'],
                'name'      => $input['name'],
                'parent_id' => $input['parent_id']
            ]);
            return redirect()->route('category.index')->with('success', \Lang::get('messages.added'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
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
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function edit($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $categories = Category::pluck('name', 'id')->toArray();
        $parent = Category::select('id','name','parent_id')->get()->toArray();
        $taxes= Tax::whereIsActive(1)->where('tax_type', '!=', '2')
            ->pluck('tax_name', 'id')->toArray();

        foreach($parent as $p => $value)
        {
            if($value['parent_id'] =='0') {
                $parentCategory[$value['id']]= $value['name'];
            } else{
                $parentCategory[$value['id']] = $categories[$value['parent_id']].'/'. $value['name'];
            }
        }

        return view('category.edit', compact('category', 'categories', 'parentCategory','taxes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryRequest  $request
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $categoryId)
    {
        $input = $request->all();
        try {
            if (isset($input['is_active'])) {
                $input['is_active'] = 1;
            } else {
                $input['is_active'] = 0;
            }
            Category::findOrFail($categoryId)->update($input);
            return redirect()->route('category.index')->with('success',  \Lang::get('messages.updated'));
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
