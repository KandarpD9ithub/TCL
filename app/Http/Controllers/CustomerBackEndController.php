<?php
/**
 * @package App/Http/Controllers
 *
 * @class CustomerController
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */


namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests\CustomerRequest;
use App\Order;
use App\Employee;
use App\Product;
use App\ProductPrice;
use App\Country;
use App\DeviceType;
use App\CustomerDevices;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Validator;
use App\CustomerWalletHistory;
use Illuminate\Support\Facades\Response;

class CustomerBackEndController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //listing of customers
        $customers = Customer::paginate(10);
        return view('customer.index',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //show customer's form
        $countries = Country::pluck('name', 'id')->toArray();
        return view('customer.create',compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $input = $request->all();   
        try {
            if ($file = $request->hasFile('profile_picture')) {
                $file=$request->file('profile_picture');
                $file_name=$this->UploadProfilePicture($file);//call to function UploadProfilePicture to upload profile picture
                $input['profile_picture']=$file_name;
            }
            $customer = Customer::create($input);
            return redirect()->route('customer.index')->with('success',  \Lang::get('messages.added'));
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
     * function call to get details of customer
     *
     * @param  int  $customerId and $api
     * @return \Illuminate\Http\Response
     */
    public function edit($customerId)
    {
        $return_data=$this->CustomerBandDetails($customerId,'');
       // dd($return_data);
        $countries = $return_data['countries'];
        $customer = $return_data['customer'];
        $band_details = $return_data['band_details'];
        $tlc_wallet_info = $return_data['tlc_wallet_info'];  
        //dd($tlc_wallet_info);      
        //$current_balance = $return_data['current_balance'];
        $payment_modes = $return_data['payment_modes'];
        $customers_history = $return_data['customers_history'];
        return view('customer.edit',compact('countries','customer','band_details','tlc_wallet_info','payment_modes','customers_history'));
    }
    /**
     * Show the form for editing the specified resource.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function CustomerBandDetails($customerId,$api="")
    {
        $countries = Country::pluck('name', 'id')->toArray();//get country list
        $customer = Customer::where('id',$customerId)->first();//get particular customer's details
        $band_details = DB::table('device_pool')
                        ->leftjoin('customer_device','device_pool.id','=','customer_device.device_pool_id')
                        ->where('customer_device.customer_id',$customerId)
                        //->where('device_pool.status', 2)
                        ->select('device_pool.original_UUID','customer_device.issued_at','device_pool.status','customer_device.is_active','customer_device.customer_id','customer_device.id','customer_device.UUID','customer_device.comment', 'customer_device.deleted_at')
                        ->first();//get band details    

        // Fetch last wallet transaction of the customer
        $tlc_wallet_info=DB::table('customer_wallet_history')
                        ->leftjoin('customer_device','customer_wallet_history.customer_device_id','=','customer_device.id')
                        /*->leftjoin('orders','customer_device.customer_id','=','orders.customer_id')*/
                        ->where('customer_device.customer_id',$customerId)
                        ->orderBy('customer_wallet_history.id','DESC')
                        //->orderBy('orders.id','DESC')
                        ->select('customer_wallet_history.credit_amount','customer_wallet_history.debit_amount','customer_wallet_history.created_at','customer_wallet_history.payment_mode',
                            //'orders.transaction_id',
                            'customer_wallet_history.id','customer_wallet_history.customer_device_id','customer_wallet_history.customer_device_id')
                        ->first();//get tcl wallet information
                $credit = 0;
                $debit = 0;
            
            if (count($band_details) > 0) {
                // If band is ever assigned to the customer
                $band_details->original_UUID = $band_details->UUID;
            }   
            if (count($tlc_wallet_info)) {
                // Fetch all wallet history of customer
                $customerRecord = CustomerWalletHistory::where('customer_device_id',$tlc_wallet_info->customer_device_id)->get();

                foreach ($customerRecord as $key => $wallet) {
                    $credit += $wallet->credit_amount;
                    $debit += $wallet->debit_amount;
                }
                $tlc_wallet_info->balance_amount = round(($credit - $debit), 2);
            }
            


        $payment_modes = array(
                            ''=>'Select Mode',
                            1=>'Cash',
                            2=>'Card',
                            3=>'PayTM',
                        );
        $date = Carbon::today()->subDays(30);
        $customers_history = DB::table('customer_wallet_history')
                             ->leftjoin('customer_device','customer_wallet_history.customer_device_id','=','customer_device.id')
                             ->where('customer_wallet_history.created_at', '>=', $date)
                             ->whereNotNull('customer_wallet_history.debit_amount')                             
                             ->leftjoin('orders','customer_device.customer_id','=','orders.customer_id')
                             ->select('orders.transaction_id','customer_wallet_history.created_at','customer_wallet_history.debit_amount','customer_wallet_history.credit_amount')
                             ->where('customer_device.customer_id',$customerId)
                             ->where('orders.customer_id',$customerId)
                             ->where('orders.payment_method',4)                             
                             ->groupBy('orders.id')
                             ->paginate(10);
        $a=['countries'=>$countries,'customer'=>$customer,'band_details'=>$band_details,'tlc_wallet_info'=>$tlc_wallet_info,'payment_modes'=>$payment_modes,'customers_history'=>$customers_history];
        
            return $a;
       // return view('customer.edit',compact('countries','customer','band_details','tlc_wallet_info','payment_modes','customers_history'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(CustomerRequest $request, $customerId)
    {
        $input = $request->all();//get all request
        try {
        if($file = $request->hasFile('profile_picture')){
            $file=$request->file('profile_picture');
                $file_name=$this->UploadProfilePicture($file);//call to function UploadProfilePicture to upload profile picture
                $input['profile_picture']=$file_name;
            }else{
                unset($input['profile_picture']);
            }
            $customer = Customer::findOrFail($customerId)->update($input);//update customer details
            return redirect()->route('customer.index')->with('success',  \Lang::get('messages.updated'));
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
     * common function to upload profile image to upload folder
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function UploadProfilePicture($file)
    {
        if($file != null){
            $destinationPath = public_path().'/upload'; // upload path
            $extension = $file->getClientOriginalExtension(); // getting image extension
            $files=explode('.', $file->getClientOriginalName());
            $fileName = $files[0].time().'.'.$extension;
            $file->move($destinationPath,$fileName); // uploading file to given path
        return $fileName;
        }
    }
    /**
     * recharge add to the customer_wallet_history table
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recharge(Request $request)
    {
        $this->validate($request,[
            'debit_amount' => 'required',
            'payment_mode'  => 'required',
        ]); 
        return redirect()->back()->with('success',  \Lang::get('messages.added'));;
    }

    /**
     * Issue band add to the table
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function issue_band(Request $request)
    {
        $this->validate($request,[
            'unique_id' => 'required',
            'UUID'  => 'required',
        ]); 
        return redirect()->back()->with('success',  \Lang::get('messages.added'));;
    }
    

    /**
     * Issue band add to the table
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update_comment(Request $request,$customerId)
    {        
       $this->validate($request, [
            'comment' => 'required|max:255',
        ]);

        try {
            $customer_device=CustomerDevices::where('id',$customerId)->first();
            $customer_device->comment=$request->get('comment');
            if($customer_device != null and $customer_device->is_active == 1){
                $customer_device->is_active=0;
                $customer_device->save();
            return redirect()->route('customer.index')->with('success',  \Lang::get('messages.inactivated'));
            }else{
                $customer_device->is_active=1;
                $customer_device->save();
                return redirect()->route('customer.index')->with('success',  \Lang::get('messages.activated'));
            }
        } catch (\Exception $error) {
            return redirect()->back()->with('error',  \Lang::get('messages.internal_error'));
        }
    }
}
