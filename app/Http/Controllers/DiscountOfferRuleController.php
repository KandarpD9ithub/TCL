<?php

namespace App\Http\Controllers;

use App\Category;
use App\DiscountOfferRule;
use App\Http\Requests\DiscountOfferRuleRequest;
use App\Product;
use Illuminate\Http\Request;

class DiscountOfferRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = DiscountOfferRule::paginate(10);
        return view('discountOffer.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('is_active','1')->pluck('name', 'id')->toArray();
        $products = Product::where('is_active','1')->where(function($query) {
        $query->where('effective_from', NULL)->orWhere('effective_from' ,"<=", date("Y-m-d"));
        })->where(function ($query) {
        $query->where('effective_to', NULL)->orWhere('effective_to',">=", date("Y-m-d"));
        })->pluck('name', 'id')->toArray();
        $parent = Category::where('is_active','1')->select('id','name','parent_id')->get()->toArray();
        $parentCategory = [];
        if (!empty($parent)) {
            foreach($parent as $p => $value)
            {
                if($value['parent_id'] =='0') {
                    $parentCategory[$value['id']]= $value['name'];
                } else{
                    if(array_key_exists($value['parent_id'], $categories)){
                        $parentCategory[$value['id']] = $categories[$value['parent_id']].'/'. $value['name'];
                    }

                }
            }
        }
        return view('discountOffer.create', compact('products', 'categories', 'parentCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  DiscountOfferRuleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiscountOfferRuleRequest $request)
    {
        $input = $request->all();
        try {
            \DB::beginTransaction();
            $input['conditions']    = json_encode($input['conditions']);
            $input['from_date']     = date('Y-m-d', strtotime($input['from_date']));
            $input['to_date']       = date('Y-m-d', strtotime($input['to_date']));
            if ($input['from_date'] <= $input['to_date']) {
                if($input['rule_type'] == 'discount'){
                    DiscountOfferRule::create($input);
                } else {
                    DiscountOfferRule::create([
                        'rule_type' => $input['rule_type'],
                        'name' => $input['name'],
                        'from_date' => $input['from_date'],
                        'to_date' => $input['to_date'],
                        'description' => $input['description'],
                        'amount_type' => $input['amount_type_offer'],
                        'amount' => $input['amount_offer'],
                        'discount_qty_step' => $input['discount_qty_step'],
                        'conditions' => $input['conditions']
                    ]);
                }

            } else {
                return back()->with('error',  \Lang::get('messages.valid_date'));
            }
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
            return back()->with('error',  \Lang::get('messages.internal_error'));
        }
        return redirect()->route('rules.index')->with('success',  \Lang::get('messages.added'));
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
     * @param  int  $ruleId
     * @return \Illuminate\Http\Response
     */
    public function edit($ruleId)
    {
        $rule = DiscountOfferRule::findOrFail($ruleId);
        $condition = json_decode($rule->conditions, true);
        $categories = Category::where('is_active','1')->pluck('name', 'id')->toArray();
        $products = Product::where('is_active','1')->pluck('name', 'id')->toArray();
        $parent = Category::where('is_active','1')->select('id','name','parent_id')->get()->toArray();
        foreach($parent as $p => $value)
        {
            if($value['parent_id'] =='0') {
                $parentCategory[$value['id']]= $value['name'];
            } else{
                if(array_key_exists($value['parent_id'], $categories)){
                    $parentCategory[$value['id']] = $categories[$value['parent_id']].'/'. $value['name'];
                }
            }
        }
        return view('discountOffer.edit', compact('products', 'categories', 'rule', 'condition','parentCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  DiscountOfferRuleRequest  $request
     * @param  int  $ruleId
     * @return \Illuminate\Http\Response
     */
    public function update(DiscountOfferRuleRequest $request, $ruleId)
    {
        $input = $request->all();
        try {
            \DB::beginTransaction();
            $input['conditions']    = json_encode($input['conditions']);
            $input['from_date']     = date('Y-m-d', strtotime($input['from_date']));
            $input['to_date']       = date('Y-m-d', strtotime($input['to_date']));
            if (isset($input['is_active'])) {
                $input['is_active'] = 1;
            } else {
                $input['is_active'] = 0;
            }
            if ($input['from_date']<=$input['to_date']) {
                if($input['rule_type'] == 'discount'){
                    DiscountOfferRule::findOrFail($ruleId)->update($input);
                } else {
                    DiscountOfferRule::findOrFail($ruleId)->update([
                        'rule_type' => $input['rule_type'],
                        'name' => $input['name'],
                        'from_date' => $input['from_date'],
                        'to_date' => $input['to_date'],
                        'description' => $input['description'],
                        'amount_type' => $input['amount_type_offer'],
                        'amount' => $input['amount_offer'],
                        'discount_qty_step' => $input['discount_qty_step'],
                        'conditions' => $input['conditions'],
                        'is_active' => $input['is_active']
                    ]);
                }
            } else {
                return back()->with('error',  \Lang::get('messages.valid_date'));
            }
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
            return back()->with('error',  \Lang::get('messages.internal_error'));
        }
        return redirect()->route('rules.index')->with('success',  \Lang::get('messages.added'));
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
