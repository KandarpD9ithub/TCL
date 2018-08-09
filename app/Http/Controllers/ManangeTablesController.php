<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ManageTable;
use App\Employee;

class ManangeTablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manageTablesData = ManageTable::paginate(10);
        
        return view('manageTables.index',compact('manageTablesData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manageTables.create');
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
            'name'  => 'required|max:50|unique:manage_tables,name',
        ]);
           
        $input = $request->all();
        try {
            \DB::beginTransaction();
             $franchise_id=Employee::where('user_id',\Auth::user()->id)->select('franchise_id')->first();
            ManageTable::create([
                'name' => $input['name'],
                'franchise_id'  => $franchise_id->franchise_id,
                'created_by'    => \Auth::user()->id,
            ]);
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
             return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
        return redirect()->route('manage-tables.index')->with('success',  \Lang::get('messages.added'));
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
         $manageTablesData = ManageTable::find($id);
        return view('manageTables.edit',compact('manageTablesData'));
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
            'name'  => 'required|max:50|unique:manage_tables,name,'.@$id,
        ]);
        $input = $request->all();
        try {
            \DB::beginTransaction();
            $franchise_id=Employee::where('user_id',\Auth::user()->id)->select('franchise_id')->first();
            ManageTable::where('id',$id)->update([
                'name' => $input['name'],
                'franchise_id'  => $franchise_id->franchise_id,
                'updated_by'    => \Auth::user()->id,
            ]);
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
             return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
        return redirect()->route('manage-tables.index')->with('success',  \Lang::get('messages.added'));
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
     * active inactive table .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activeInactive($id)
    {
        $manageTablesData = ManageTable::find($id);
        try {
            if($manageTablesData->is_active == 1){
                $data = ['is_active' => '0', ];
            }else{
                $data = ['is_active' => '1', ];
            }            
            $dd = ManageTable::where('id',$id)->update($data);

            return redirect()->route('manage-tables.index')->with('success', \Lang::get('messages.updated'));
        } catch (\Exception $error) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
    }

}
