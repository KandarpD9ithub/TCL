<?php

namespace App\Http\Controllers;

use App\Country;
use App\Franchise;
use App\Http\Requests\FranchiseRequest;
use App\Tax;
use Illuminate\Http\Request;

class FranchiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $franchises = Franchise::paginate(10);
        $countries = Country::pluck('name', 'id')->toArray();
        return view('franchise.index', compact('franchises', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::pluck('name', 'id')->toArray();
        return view('franchise.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FranchiseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FranchiseRequest $request)
    {
        $input = $request->all();
        try {
            $franchiseId = Franchise::create([
                'name'              => $input['name'],
                'address_line_one'  => $input['address_line_one'],
                'address_line_two'  => $input['address_line_two'],
                'city'              => $input['city'],
                'region'            => $input['region'],
                'country_id'        => $input['country_id'],
                'gst_number'        => $input['gst_number']
            ])->id;

            /*$taxes = array_values($input['tax']);
            if (count($input['tax']) > 0) {
                foreach ($taxes as $tax) {
                    if (!empty($tax['rate'])) {
                        Tax::create([
                            'franchise_id' => $franchiseId,
                            'tax_name'  => $tax['name'],
                            'tax_rate'  => $tax['rate']
                        ]);
                    }
                }
            }*/
            return redirect()->route('franchise.index')->with('success',  \Lang::get('messages.added'));
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
     * @param  int  $franchiseId
     * @return \Illuminate\Http\Response
     */
    public function edit($franchiseId)
    {
        $franchise = Franchise::whereId($franchiseId)->with('taxes')->first();
        /*$taxRate = [];
        foreach ($franchise->taxes as $taxes) {
            $taxRate[$taxes->tax_name] = $taxes->tax_rate;
        }*/
        $countries = Country::pluck('name', 'id')->toArray();
        return view('franchise.edit', compact('franchise', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FranchiseRequest  $request
     * @param  int  $franchiseId
     * @return \Illuminate\Http\Response
     */
    public function update(FranchiseRequest $request, $franchiseId)
    {
        $input = $request->all();
        //dd($input);
        try {
            Franchise::findOrFail($franchiseId)->update([
                'name'              => $input['name'],
                'address_line_one'  => $input['address_line_one'],
                'address_line_two'  => $input['address_line_two'],
                'city'              => $input['city'],
                'region'            => $input['region'],
                'country_id'        => $input['country_id'],
                'gst_number'        => $input['gst_number']
            ]);
           /* Tax::whereFranchiseId($franchiseId)->delete();
            $taxes = array_values($input['tax']);
            if (count($input['tax']) > 0) {
                foreach ($taxes as $tax) {
                    if (!empty($tax['rate'])) {
                        Tax::create([
                                'franchise_id' => $franchiseId,
                                'tax_name'  => $tax['name'],
                                'tax_rate'  => $tax['rate']
                        ]);
                    }
                }
            }*/
            return redirect()->route('franchise.index')->with( \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return back()->with('error',  \Lang::get('messages.internal_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $franchiseId
     * @return \Illuminate\Http\Response
     */
    public function destroy($franchiseId)
    {
        try {
            Franchise::findOrfail($franchiseId)->delete();
            return redirect()->back()->with('success', \Lang::get('messages.deleted'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
    }
}
