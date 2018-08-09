<?php
/**
 * @package App/Http/Controllers
 *
 * @class TaxController
 *
 * @author Bhavana <bhavana@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App\Http\Controllers;
use App\Http\Requests\TaxRequest;
use App\Tax;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxes = Tax::latest()->paginate(10);
        $taxType = \Config::get('constants.TAX_TYPE');
        return view('taxes.index', compact('taxes', 'taxType'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('taxes.create');

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  TaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxRequest $request)
    {
        $input = $request->all();
        try{
            Tax::create($input);
        return redirect()->route('taxes.index')
            ->with('success',\Lang::get('messages.added'));
    } catch (\Exception $error) {
            return redirect()->back()->with('error',\Lang::get('messages.internal_error'));
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
        $tax = Tax::find($id);
        return view('taxes.show', compact('tax'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tax = Tax::find($id);

        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TaxRequest  $request
     * @param  Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update(TaxRequest $request, Tax $tax)
    {
        $input = $request->all();
        try {
            if (isset($input['is_active'])) {
                $input['is_active'] = 1;
            } else {
                $input['is_active'] = 0;
            }
            $tax->update($input);
            return redirect()->route('taxes.index')
                ->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error', \Lang::get('messages.internal_error'));
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
        Tax::find($id)->delete();

        return redirect()->route('taxes.index')
            ->with('success', \Lang::get('messages.deleted'));
    }

}