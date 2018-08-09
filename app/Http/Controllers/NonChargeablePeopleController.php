<?php
/**
 * @package App/Http/Controllers
 *
 * @class NonChargeablePeopleController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2017 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;
use App\NonChargeablePeople;
use Illuminate\Http\Request;

class NonChargeablePeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ncPeoples = NonChargeablePeople::paginate(10);
        return view('nonChargeable.index', compact('ncPeoples'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('nonChargeable.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'  => 'required|regex:/^[a-zA-Z\s]*$/|max:150|unique:non_chargeable_peoples,name'
        ]);
        $input = $request->all();
        try {

            NonChargeablePeople::create($input);
            return redirect()->route('non-chargeable.index')->with('success', \Lang::get('messages.added'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     ** @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function edit($id)
    {
        $ncPeoples = NonChargeablePeople::find($id);
        return view('nonChargeable.edit', compact('ncPeoples'));
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
        $this->validate($request,[
            'name'  => 'required|regex:/^[a-zA-Z\s]*$/|max:150|unique:non_chargeable_peoples,name,'.@$id,
        ]);
        $input = $request->all();
        try {
            NonChargeablePeople::where('id',$id)->update(['name'=>$input['name']]);
            return redirect()->route('non-chargeable.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
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

    /**
     * @return mixed
     */
    public function allList()
    {
        $ncPeoples = NonChargeablePeople::whereIsActive(1)->get();
        return response()->success(['ncPeoples' => $ncPeoples]);
    }
    /**
     * active inactive non-chargable products
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function activeInactive($id)
    {
        $ncPeoples = NonChargeablePeople::find($id);
        try {
            if($ncPeoples->is_active == 1){
                $data = ['is_active' => 0, ];
            }else{
                $data = ['is_active' => 1, ];
            }
            NonChargeablePeople::where('id',$id)->update($data);
            return redirect()->route('non-chargeable.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
    }
}
