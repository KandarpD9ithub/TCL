<?php
/**
 * @package App/Http/Controllers
 *
 * @class CustomerController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;

use App\Customer;
use App\Http\Controllers\CustomerBackEndController;
use App\Http\Requests\CustomerRequest;
use App\Order;
use App\Employee;
use App\Product;
use App\ProductPrice;
use Illuminate\Http\Request;
use Hash;
use DB;
use Illuminate\Validation\Validator;
use Mail;
use App\User;
use Carbon\Carbon;
use App\DeviceType;
use App\CustomerWalletHistory;
use App\DevicePool;
use App\CustomerDevices;
use App\CustomerOTP;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::with('orders', 'country')->get()->toArray();
        return response()->success(['customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rule = [
            'name'  =>  'required|max:100|regex:/^[a-zA-Z\s]*$/',
            'contact_number' => 'required|numeric|regex:/^[0-9]{10}$/|unique:customers,contact_number',
            'email' => 'max:150|email|unique:customers,email',
            'profile_picture'   => '',
            'address_line_one'  => 'max:100',
            'address_line_two'  => 'max:100',
            'city'              => 'max:50',
            'region'            => 'max:50',
            'country_id'        => '',
        ];
        $validator =  \Illuminate\Support\Facades\Validator::make($input, $rule);
        if ($validator->fails()) {
            return Response::json(array(
                'error' => true,
                'status_code' => 422,
                'message' => $validator->messages(),
            ));
        } else {
            try{
                $customer = Customer::create($input);
            }catch(\Exception $error){
                return response()->error('Internal server error.');
            }
            $customerData =$this->getFullDetails($customer->id);
            return response()->success(['customer' => $customerData]);
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
        $orders =  Order::with('orderDetail')->whereId($id)->get()->toArray();
        $customer = Customer::pluck('name', 'id')->toArray();
        $productName = Product::pluck('name', 'id')->toArray();
        $productId = $quantity = $viewOrder = $detail = [];
        $discount ='';
        $productTotal = [];
        foreach ($orders as $order) {
            foreach($order['order_detail'] as $orderDetail) {
                $productId[] = $orderDetail['product_id'];
                $quantity[] = $orderDetail['quantity'];
                $franchiseId= Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
                $franchiseProductPrice = $franchiseProductId = [];
                if (!empty($productPrice)) {
                    foreach ($productPrice as $price) {
                        $franchiseProductId[$price['product_id']] = $price['price'];
                        $franchiseProductPrice[] = $price['product_id'];
                    }
                }
                if (in_array($orderDetail['product_id'], $franchiseProductPrice)) {
                    $productsPrices = $franchiseProductId[$orderDetail['product_id']];
                } else {
                    $productsPrices = Product::where('id', $orderDetail['product_id'])->first()->price;
                }
                $detail[]  = [
                    'id' => $orderDetail['id'],
                    'product_id' => $orderDetail['product_id'],
                    'product_name' => $productName[$orderDetail['product_id']],
                    'product_price' => $productsPrices,
                    'quantity' => $orderDetail['quantity']
                ];
                $productTotal[] = ($productsPrices * $orderDetail['quantity']);
            }
            $viewOrder = [
                'id' =>  $order['id'],
                'customer_id' => $order['customer_id'],
                'customer_name' => $customer[$order['customer_id']],
                'created_at' => $order['created_at'],
                'order_number' => $order['order_number'],
                'order_detail'  => $detail
            ];
        }
        $totalPrice = array_sum($productTotal);
        $viewOrder['total'] =  getTaxes($productId, $totalPrice, date('Y-m-d', strtotime($viewOrder['created_at'])));
        return $viewOrder['total'];
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
     * @param Request $request
     * @param $customerId
     * @return mixed
     */
    public function update(Request $request, $customerId)
    {
        $input = $request->all();
        $rule = [
            'name'  =>  'required|max:100|regex:/^[a-zA-Z\s]*$/',
            'contact_number' => 'required|numeric|regex:/^[0-9]{10}$/|unique:customers,contact_number, '.$customerId,
            'email' => 'max:150|email|unique:customers,email,'.$customerId,
            'profile_picture'   => '',
            'address_line_one'  => 'max:100',
            'address_line_two'  => 'max:100',
            'city'              => 'max:50',
            'region'            => 'max:50',
            'country_id'        => '',
        ];
        $validator =  \Illuminate\Support\Facades\Validator::make($input, $rule);
        if ($validator->fails()) {
            return Response::json(array(
                'error' => true,
                'status_code' => 422,
                'message' => $validator->messages(),
            ));
        } else {
            try {
                $customer = Customer::findOrFail($customerId)->update($input);
            } catch (\Exception $error) {
                return Response::json(array(
                    'error' => true,
                    'status_code' => 500,
                    'message'   => $error->getMessage()
                ));
            }
            $customerData =$this->getFullDetails($customerId);
            return Response::json(array(
                'success' => true,
                'status_code' => 201,
                'customer' => $customerData,
            ));
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


    public function customerOrder($id)
    {
        $orders = Order::with('orderDetail.product','customer', 'employee.user', 'manageTable')->where('customer_id', $id)->get()->toArray();
       /* $Price=[];
        foreach($orders as $order)
        {
            $Price[] = $this->show($order['id']);
        }
        $ordersPrice = MergeArrays($orders, $Price);
        dd($ordersPrice);*/
        return response()->success(['orders' => $orders]);
    }

    public function change_password(Request $request)
    {
        $this->validate($request, [
            'new_password' => 'required',
        ]);
        $request_data = $request->All();
        try {
                $current_password = \Auth::User()->password;
                    $user_id = \Auth::User()->id;
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['new_password']);;
                    $obj_user->save();
        } catch (\Exception $error) {
            return Response::json(array(
                    'error' => true,
                    'status_code' => 500,
                    'message'   => $error->getMessage()
                ));
        }
        return Response::json(array(
                    'success' => true,
                    'status_code' => 201,
                    'user' => $obj_user,
                ));
    }

    /**
     * search user bye email or mobile_number
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  user_name
     * @return \Illuminate\Http\Response
     */
    public function searchUser(Request $request)
    {
        $this->validate($request, [
            'user_name' => 'required',
        ]);
        $input=$request->all();

        $string = $input['user_name'];    
        $regex = '/@/';    
        $result = preg_match($regex, $string, $matches);
        
        if(count($matches) >0){
            $customer = Customer::where('email',$string);
        }else{
            $customer = Customer::where('contact_number',$string);
        }
        $customers = $customer->get()->toArray();
        if(count($customers) >0){

            foreach ($customers as $key => $value) {
                 $band_details = DeviceType::join('device_pool','device_types.id','=','device_pool.device_type_id')
                            ->leftjoin('customer_device','device_pool.id','=','customer_device.device_pool_id')
                            ->where('customer_device.customer_id',$value['id'])
                            ->whereNull('customer_device.deleted_at')
                            //->where('device_pool.status',2)
                            ->select('device_pool.original_UUID','customer_device.issued_at','device_pool.status','customer_device.is_active','customer_device.customer_id','customer_device.id')
                            ->first();//get band details
                            $band=$band_details;
                        $value['profile_picture'] = !$value['profile_picture'] == null ? \URL::to('/').'/upload/'.$value['profile_picture'] : "";
                        $country_name= $this->setCountry($value['country_id']);
                        $value['country_name'] = $country_name;
                        if($band!=null){

                            $total = $this->checkBalance($band->id);//check customer balance
                                $band->total_balance = $total;
                            if($band->status == 1){
                                $band->status = \Lang::get('views.new');
                            }
                            elseif($band->status == 2){
                                $band->status = \Lang::get('views.in_use');
                            }
                            elseif($band->status == 3){
                                $band->status = \Lang::get('views.damaged');
                            }
                            elseif($band->status == 4){
                                $band->status = \Lang::get('views.lost');
                            }
                            if($band->is_active== 1){
                                $band->is_active = \Lang::get('views.active');
                            }else{
                                $band->is_active = \Lang::get('views.inactive');
                            }
                        }
                            $value['customer_device_details']=$band;
                $band_data[]=$value;
            }
            if(isset($band_data)) {
                return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'user' => $band_data,
                    ));
            }else{
                return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'user' => [],
                    ));
            }
        }else{
            return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message' => 'User not found',
                    ));
        }
    }
    /**
     * get country name
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  country_id
     * @return \Illuminate\Http\Response
     */
    public function setCountry($countryId)
    {
        $country = DB::table('countries')->where('id',$countryId)->first();
        if($country != null){
            $contry_name = $country->name;
        }else{
            $contry_name = "";
        }
        return $contry_name;
    }
    /**
     * get customer wallet balance
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  deviceId(customer's device id)
     * @return \Illuminate\Http\Response
     */

    public function checkBalance($deviceId)
    {   
        $credit = 0;
        $debit = 0;
        $customerRecord = CustomerWalletHistory::where('customer_device_id',$deviceId)->get();
                                if (count($customerRecord) >0 ) {
                                    foreach ($customerRecord as $key => $wallet) {
                                        $credit += $wallet->credit_amount;
                                        $debit += $wallet->debit_amount;
                                    }
                                    $total = $credit - $debit;
                                }else{
                                    $total = 0;
                                }
            return $total;
    }
    /**
     * get customer wallet balance
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  deviceId(customer's device id)
     * @return \Illuminate\Http\Response
     */

    public function checkBalanceWithPoint($deviceId)
    {   
        $credit = 0;
        $debit = 0;
        $customerRecord = CustomerWalletHistory::where('customer_device_id',$deviceId)->get();
                                if (count($customerRecord) >0 ) {
                                    foreach ($customerRecord as $key => $wallet) {
                                        $credit += $wallet->credit_amount;
                                        $debit += $wallet->debit_amount;
                                    }
                                    $total = $credit - $debit;
                                }else{
                                    $total = 0;
                                }
            return number_format($total,2);
    }
    /**
     * get customer band details
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  customer_id
     * @return \Illuminate\Http\Response
     */

    public function CustomerBandDetails(CustomerBackEndController $CustomerBackEndController,Request $request)
    {
        $input = $request->all();
        $this->CustomerBackEndController = $CustomerBackEndController;
        $a = $this->CustomerBackEndController->CustomerBandDetails($input['customer_id'],1);

        return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'band_details' => $a,
                    ));
    }
    public function getCountries()
    {
        $country = DB::table('countries')
                    ->get();
        if(count($country) > 0)   {
            return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'country' => $country,
                    ));
        }else{
            return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'country' => '',
                    ));
        }
    }
    /**
     * add customer band details
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  customer_id,device_type_id,original_UUID,
     * @return \Illuminate\Http\Response
     */
    public function createBandDetails(Request $request)
    {
        $input = $request->all();
        try{
            \DB::beginTransaction();
            $customerPool = DevicePool::where('original_UUID',$input['original_UUID'])/*->where('status',1)*/->first();
            if(count($customerPool) > 0){
                if ($customerPool->status == '3' || $customerPool->status == '4') {
                     return response()->error(['message' => 'device is lost or damaged']);   
                } else {
                    $device_pool = DevicePool::where('id',$customerPool->id)->update([
                        'status'           => 2,
                    ]); 
                    $customerPoolId = $customerPool->id;
                }
            
            } else{ 
                $device_pool = DevicePool::create([
                    'device_type_id'   => $input['device_type_id'],
                    'original_UUID'    => $input['original_UUID'],
                    'status'           => 2,
                ]);
                $customerPoolId = $device_pool->id;
            }
            $new_uuid = $input['original_UUID'];
            $customer = DB::table('customer_device')->where('customer_id',$input['customer_id'])->first();
            if(count($customer) > 0){
                $customer_device = CustomerDevices::where('customer_id',$input['customer_id'])->withTrashed()->first();
                /*$customer_device = CustomerDevices::where('customer_id',$input['customer_id'])->update([
                    'device_pool_id'=> $customerPoolId,
                    'UUID'          => $new_uuid,
                    'pin'          => Hash::make($input['pin']),
                    'issued_at'     => date('Y-m-d H:i:s'),
                    'is_active'     => 1,
                    'deleted_at'    => null,
                ]);*/
                if ($customerPoolId) {
                    $customer_device->UUID = $new_uuid;
                    $customer_device->device_pool_id = $customerPoolId;
                    $customer_device->pin = Hash::make($input['pin']);
                    $customer_device->is_active = 1;
                    $customer_device->deleted_at = null;
                    $customer_device->issued_at = date('Y-m-d H:i:s');
                    $customer_device->save();
                }
                $action = 2;
            }else{
                $customer_device = CustomerDevices::create([
                    'customer_id'   => $input['customer_id'],
                    'device_pool_id'=> $customerPoolId,
                    'UUID'          => $new_uuid,
                    'pin'          => Hash::make($input['pin']),
                    'issued_at'     => date('Y-m-d H:i:s'),
                    'is_active'     => 1,
                ]);
             $action = 1;   
            }
            $this->SendMailAndSMS($input['customer_id'],$action);//send mail and sms to the user
            \DB::commit();
        }catch(\Exception $error){
            \DB::rollBack();
            return response()->error(['message' => $error->getMessage()]);
        }
        $customer = CustomerDevices::where('customer_id',$input['customer_id'])->first();
        $customer->new_UUID = $new_uuid;
        return response()->success(['message' => 'Device Added successfully', 'customer_device' => $customer]);
    }
    /**
     * get customer band details
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  customer_id,original_UUID,
     * @return \Illuminate\Http\Response
     */
    public function CheckBandDetails(Request $request)
    {
        $input = $request->all();
        if (isset($input['customer_id'])) {
            
            $check_user_exist = DB::table('customer_device')
                                ->leftjoin('customers','customer_device.customer_id','=','customers.id')
                                ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id')
                                ->where('customer_device.customer_id',$input['customer_id'])
                                ->where('device_pool.original_UUID',$input['original_UUID'])
                                ->whereNull('customer_device.deleted_at')
                                ->where('device_pool.status',2)
                                ->select('customer_device.device_pool_id','customer_device.UUID','customer_device.issued_at','customer_device.is_active','customer_device.id','customer_device.customer_id','customers.name','customers.contact_number','customers.email','customers.profile_picture','customers.name')
                                ->first();
                if($check_user_exist == null){
                    // if band is returned or never assigned
                    $check_user = DB::table('customer_device')
                                ->leftjoin('customers','customer_device.customer_id','=','customers.id')
                                ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id')
                                ->where('customer_device.customer_id',$input['customer_id'])                         
                                ->where('device_pool.status',2)
                                ->whereNull('customer_device.deleted_at')
                                ->select('customer_device.device_pool_id','customer_device.UUID','customer_device.issued_at','customer_device.is_active','customer_device.id','customer_device.customer_id','customers.name','customers.contact_number','customers.email','customers.profile_picture','customers.name')
                                ->first();//status 2
                    if($check_user != null){
                        $status = 2;
                        $check_user_exist = $check_user;
                    }else{
                        $check_user = DB::table('customer_device')
                                ->leftjoin('customers','customer_device.customer_id','=','customers.id')
                                ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id')
                                ->where('device_pool.original_UUID',$input['original_UUID'])
                                ->whereIn('device_pool.status',[2,3,4])
                                ->whereNull('customer_device.deleted_at')
                                ->select('customer_device.device_pool_id','customer_device.UUID','customer_device.issued_at','customer_device.is_active','customer_device.id','customer_device.customer_id','customers.name','customers.contact_number','customers.email','customers.profile_picture','customers.name','device_pool.status')
                                ->first();//status 3
                        if(isset($check_user) and $check_user!= null and $check_user->status != 1){
                            $status = 3;
                            $check_user_exist = $check_user;
                        }else{
                            $checkBand = DB::table('device_pool')->where('original_UUID',$input['original_UUID'])->orderBy('id','DESC')->whereIn('status',[3,4])->first();
                            if($checkBand!=null){
                                $status = 3;
                            }else{
                                $status = 4;
                            }
                            $check_user_exist = $check_user;
                        }
                    }

                }else{
                    //dd($check_user);
                    $status = 1;
                }                
        }else{
            $check_user_exist = DB::table('customer_device')
                            ->leftjoin('customers','customer_device.customer_id','=','customers.id')
                            ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id')
                            ->where('device_pool.original_UUID',$input['original_UUID'])
                            ->whereNull('customer_device.deleted_at')
                            ->orderBy('device_pool.id','DESC')
                            ->select('customer_device.device_pool_id','customer_device.UUID','customer_device.issued_at','customer_device.is_active','customer_device.id','customer_device.customer_id','customers.name','customers.contact_number','customers.email','customers.profile_picture','customers.name','device_pool.status')
                            ->first();
            if ($check_user_exist != null) {
                    if ($check_user_exist->status == 3 or $check_user_exist->status == 4) {
                            $status = 2 ;
                            //$check_user_exist = null;
                    }elseif ($check_user_exist->status == 2) {
                        $status = 1;
                    }
            }else{
                $status = 3;
                $check_user_exist = null;
            }

        }
            if (count($check_user_exist) > 0) {
                    if($check_user_exist->is_active == 1){
                        $check_user_exist->active = 1;
                        $check_user_exist->is_active = \Lang::get('views.active');
                    }else{
                        $check_user_exist->active = 0;
                        $check_user_exist->is_active = \Lang::get('views.inactive');
                    }
                    $total = $this->checkBalance($check_user_exist->id);

                    $check_user_exist->balance = round($total, 2);
                    /*if($check_user_exist->payment_mode== 1){
                        $check_user_exist->payment_mode = \Lang::get('views.cash');
                    }elseif($check_user_exist->payment_mode== 2){
                        $check_user_exist->payment_mode = \Lang::get('views.card');
                    }elseif($check_user_exist->payment_mode== 2){
                        $check_user_exist->payment_mode = \Lang::get('views.paytm');
                    }*/
                if($check_user_exist->active == 0){
                    return Response::json(array(
                        'success' => false,
                        'status_code' => 200,
                        'user_exist' => $check_user_exist,
                    ));
                }
                if(isset($status) and $status == 1){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 1,
                                'status'    => true,
                                'status_code' => 201,
                                'user_exist' => $check_user_exist,
                                'massage'=> '',
                            ));
                }elseif(isset($status) and $status == 2){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 2,
                                'status'    => false,
                                'status_code' => 500,
                                'user_exist' => $check_user_exist,
                                'massage'=> '',
                            ));
                }elseif(isset($status) and $status == 3){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 3,
                                'status'    => false,
                                'status_code' => 500,
                                'user_exist' => $check_user_exist,
                                'massage'=> '',
                            ));
                }elseif(isset($status) and $status == 4){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 4,
                                'status'    => false,
                                'status_code' => 500,
                                'user_exist' => $check_user_exist,
                                'massage'=> '',
                            ));
                }


            return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'user_exist' => $check_user_exist,
                    ));
        }else{
          if(isset($status) and $status == 3){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 3,
                                'status'    => false,
                                'status_code' => 500,
                                'user_exist' => '',
                                'massage'=> '',
                            ));
                }elseif(isset($status) and $status == 4){
                    return Response::json(array(
                                'success' => true,
                                'status_type'=> 4,
                                'status'    => false,
                                'status_code' => 500,
                                'user_exist' => '',
                                'massage'=> '',
                            ));
                }
            return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'user_exist' => '',
                    ));
        }
    }
    /**
     * add customer pin for customer
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  customer_id,pin
     * @return \Illuminate\Http\Response
     */
    public function addCustomerPin(Request $request)
    {
        $this->validate($request, [
            'UUID' => 'required',
            'pin' => 'required',
            'confirm_pin' => 'required|same:pin',
        ]);
        $input = $request->all();
        try{
            \DB::beginTransaction();
            $customer_device = CustomerDevices::where('UUID',$input['UUID'])
                                ->update([
                                    'pin'          => Hash::make($input['pin']),
                                ]);
            \DB::commit();
        }catch(\Exception $error){
            \DB::rollBack();
            return response()->error(['message' => $error->getMessage()]);
        }
        if ($customer_device) { 
        return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'message' => 'Pin Added successfully', 
                        'customer_device' => CustomerDevices::where('UUID',$input['UUID'])->first()
                    ));           
            
        }else{
            return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'customer_device' => '',
                    ));
        }
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
     * update profile of customer
     *
     * @param  customerId , profile_picture
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function updateProfilePicture(Request $request)
    {
        $this->validate($request, [
            'profile_picture' => 'image',
        ]);
        $input = $request->all();
        try{
            if ($file = $request->hasFile('profile_picture')) {
                    $file=$request->file('profile_picture');
                    $fileName=$this->UploadProfilePicture($file);//call to function UploadProfilePicture to upload profile picture
                    $input['profile_picture']=$fileName;
                    $updateCustomer = Customer::findOrFail($input['customer_id'])->update($input);
                }
            }catch(\Exception $error)
            {
                return response()->error(['message' => $error->getMessage()]);
            }
        if(isset($updateCustomer) and $updateCustomer){
            $customerData = $this->getFullDetails($input['customer_id']);
            return response()->success(['message' => 'Profile picture updated successfully', 'customer_profile' => $customerData]);
        }else{
            return Response::json(array(
                        'success' => true,
                        'status_code' => 500,
                        'customer_profile' => 'Error to update Profile Picture',
                    ));
        }
    }
    /**
     * create wallet amount
     *
     * @param  customerId ,amount
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
        public function addWalletHistory(Request $request)
        {
            $input = $request->all();
            try{
                $customerDevice = CustomerDevices::where('customer_id',$input['customer_id'])->where('is_active',1)->first();
                if(count($customerDevice) > 0 and $input['credit_amount'] > 0){
                    $input['customer_device_id'] = $customerDevice->id;
                    $input['debit_amount'] = null;
                        $customerAddWallet = CustomerWalletHistory::create($input);
                        $this->SendMailAndSMSWallet($input['customer_id'],$customerAddWallet->customer_device_id,$input['credit_amount'],$customerAddWallet->created_at);//send mail and sms for wallet alert
                }elseif($input['credit_amount'] <= 0){
                    return Response::json(array(
                        'success' => true,
                        'status_code' => 500,
                        'customer' => 'Amount can not be 0 or less',
                    ));
                }else{
                    return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'customer' => 'Customer not found',
                    ));

                }

            }catch(\Exception $error){
                    return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => $error->getMessage()
                ));
            }
            if(isset($customerAddWallet)){
                $total = $this->checkBalance($customerAddWallet->customer_device_id); //check balance of user
                 return Response::json(array(
                        'success' => true,
                        'status_code' => 201,
                        'customer' => ['walletData'=>CustomerWalletHistory::where('id',$customerAddWallet->id)->first(),'total'=>$total],
                    ));
            }
        }

        public function getFullDetails($customerId)
        {
            $customerData = DB::table('customers')->where('id',$customerId)->first();
            
            $country_name = $this->setCountry($customerData->country_id);
            $customerData->country_name = $country_name;
            if($customerData->profile_picture != null){
                $customerData->profile_picture= \URL::to('/').'/upload/'.$customerData->profile_picture;
            }
            return $customerData;
        }
        /**
         * change User pin
         *
         * @param  customerId ,amount
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function editPin(Request $request)
        {

            $input = $request->all();
            
                $this->validate($request,[
                    'UUID'          => 'required',
                    'old_pin'       => 'required',
                    'new_pin'       => 'required',
                ]);
            try{
                DB::beginTransaction();
                $check = CustomerDevices::where('UUID',$input['UUID'])->whereNotNull('pin')->first();
                    if (count($check) > 0) {
                        $checkPin = Hash::check($input['old_pin'],$check->pin);
                        if($checkPin){
                            $user_data = ['pin' => Hash::make($input['new_pin'])];
                            $up = CustomerDevices::where('customer_id',$check->customer_id)->update($user_data);
                        }
                    }else{
                         return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message'   => 'Customer not found',
                    ));
                }
                DB::commit();
            }catch(\Exception $error){
                DB::rollBack();                
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Internal server error',
                ));
            }
            if (isset($checkPin) and $checkPin == true) {
                return Response::json(array(
                    'success' => true,
                    'status_code' => 201,
                    'message'   => 'PIN changed successfully',
                    'customer_device'  => $check,
                ));
            }else{
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Not updated',
                ));
            }
        }
        /**
         * forgot change User pin
         *
         * @param  customerId ,amount
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function changePin(Request $request)
        {

            $input = $request->all();
            
                $this->validate($request,[
                    'customer_id'          => 'required',
                    'new_pin'       => 'required',
                ]);
            try{
                DB::beginTransaction();
                $check = CustomerDevices::where('customer_id',$input['customer_id'])->first();
                    if (count($check) > 0) {
                            $user_data = ['pin' => Hash::make($input['new_pin'])];
                            $up = CustomerDevices::where('customer_id',$input['customer_id'])->update($user_data);
                    }else{
                         return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message'   => 'Customer not found',
                    ));
                }
                DB::commit();
            }catch(\Exception $error){
                DB::rollBack();                
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Internal server error',
                ));
            }
            if (isset($up) and $up == true) {
                return Response::json(array(
                    'success' => true,
                    'status_code' => 201,
                    'message'   => 'PIN changed successfully',
                    'customer_device'  => $check,
                ));
            }else{
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Not updated',
                ));
            }
        }
        /**
         * inactive pin
         *
         * @param  customerId ,amount
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function pinActiveInactive(Request $request)
        {
            $input = $request->all();
                $this->validate($request,[
                    'UUID'          => 'required',
                    'pin'           => 'required',
                ]);
            try{
                DB::beginTransaction();
                $check = CustomerDevices::where('UUID',$input['UUID'])->first();
                    if (count($check) > 0) {
                        $checkPin = Hash::check($input['pin'],$check->pin);
                        if($checkPin){
                            $user_data = ['pin' => null];
                            $up = CustomerDevices::where('customer_id',$check->customer_id)->update($user_data);
                        }
                    }else{
                         return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message'   => 'Customer not found',
                    ));
                }
                DB::commit();
            }catch(\Exception $error){
                DB::rollBack();                
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Internal server error',
                ));
            }

            if (isset($checkPin) and $checkPin == true) {
                return Response::json(array(
                    'success' => true,
                    'status_code' => 201,
                    'message'   => 'Pin inActiveted successfully',
                    'customer_device'  => CustomerDevices::where('UUID',$input['UUID'])->first(),
                ));
            }else{
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Not updated',
                ));
            }
        }
        /**
         * forgot pin and send otp to customer
         *
         * @param  customerId 
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function forgotPin(Request $request)
        {
            $input = $request->all();
            $otp=mt_rand(1000, 9999);
            try{
                $checkCustomer = CustomerOTP::where('customer_id',$input['customer_id'])->first();
                if(count($checkCustomer) > 0){
                    $up = CustomerOTP::where('customer_id',$input['customer_id'])->update([
                        'customer_id'   => $input['customer_id'], 
                        'OTP'           => $otp,
                        'expire_time'   => date('Y-m-d H:i:s'),
                    ]);
                    DB::commit();
                }else{
                    $up = CustomerOTP::create([
                        'customer_id'   => $input['customer_id'], 
                        'OTP'           => $otp,
                        'expire_time'   => date('Y-m-d H:i:s'),
                    ]);
                    DB::commit();
                }
                $customer = Customer::find($input['customer_id']);
                $name = ucfirst($customer->name);
                $msg = "Dear $name, your One Time Password is : $otp , Expired in 10 minutes. Regards TCL.";
                /*$msg = 'Dear%20'.ucfirst($customer->name).'%20your%20OTP%20is%20'.$otp;*/
                $urlSMS = 'www.mgage.solutions/SendSMS/sendmsg.php?uname=shalinitcl&pass=welcome1&send=THETCL&dest='.$customer->contact_number.'&msg='.str_replace(' ','%20',$msg);
                    $this->sendSMS($urlSMS);//send to function
            }catch(\Exception $error){
                DB::rollBack();
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Internal server error',
                ));
            }
            return Response::json(array(
                    'success' => true,
                    'status_code' => 201,
                    'message'   => 'OTP Send to your device',
                ));
        }
        /**
         * change User pin by OTP
         *
         * @param  customerId ,amount
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function checkOTP(Request $request)
        {
            $this->validate($request,[
                'OTP'   => 'required',
            ]);
            $input = $request->all();

            try{
                    $otp_data = CustomerOTP::where('customer_id',$input['customer_id'])->where('OTP',$input['OTP'])->first();
                    if(count($otp_data) > 0){
                        $endTime = strtotime("+10 minutes",strtotime($otp_data->expire_time));
                        if(strtotime(date('Y-m-d h:i:s')) > $endTime){
                            return Response::json(array(
                                'success' => false,
                                'status_code' => 500,
                                'message'   => 'OTP expired',
                            ));
                        }else{
                            return Response::json(array(
                                'success' => true,
                                'status_code' => 201,
                                'message'   => 'OTP verified',
                            ));
                        }

                    }else{
                        return Response::json(array(
                                'success' => false,
                                'status_code' => 500,
                                'message'   => 'OTP not found',
                            ));
                    }

                }catch(\Exception $error){
                    return response()->error(['message' => 'Internal server error']);

                }

        }

        /**
         * send Mail and SMS to the user
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function SendMailAndSMS($customerId,$action)
        {
            $user=DB::table('customers')
                        ->join('customer_device','customers.id','=','customer_device.customer_id')
                        ->select('customers.name','customers.contact_number','customer_device.issued_at','customers.email','customer_device.id','customer_device.UUID')
                        ->where('customers.id',$customerId)
                        ->first();
            if($user != null){

                    $email = $user->email;
                    $total = $this->checkBalanceWithPoint($user->id);//check customer balance
                    $user->total = $total;
                    $name = ucfirst($user->name);
                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $user->issued_at, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                    if($action == 1){
                        $issue = 'issued';
                    }else{
                        $issue = 're-issued';
                    }
                    $msg = "​Dear $name, band has been successfully $issue on $date. Available balance is $total. Regards TCL.";
                    /*$msg = 'Dear '.ucfirst($user->name).', band '.$user->UUID.' has been successfully issued on '.date('d/m/y H:i',strtotime($user->issued_at)).'. Available balance is '.$total;*/
                    $urlSMS = 'www.mgage.solutions/SendSMS/sendmsg.php?uname=shalinitcl&pass=welcome1&send=THETCL&dest='.$user->contact_number.'&msg='.str_replace(' ','%20',$msg);
                    $this->sendSMS($urlSMS);//send to function                    
                if ($email != null or $email != "") {
                    $a=Mail::send('emails.band_issue',['user'=>$user],function($message) use ($email,$issue) {
                            $message->to($email,$email)->subject("Band has been successfully $issue"); //send mail to the users for register link
                        });
                }
            }
        }
        /**
         * send Mail and SMS to the user
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function SendMailAndSMSWallet($customerId,$deviceId,$add_amount,$dates)
        {
            $add_amountWithoutPoint = $add_amount;  
            $add_amount = number_format($add_amount,2);
            $user=DB::table('customers')
                        ->join('customer_device','customers.id','=','customer_device.customer_id')
                        ->select('customers.name','customers.contact_number','customer_device.issued_at','customers.email','customer_device.id','customer_device.UUID')
                        ->where('customers.id',$customerId)
                        ->first();
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $dates, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
            $total = $this->checkBalanceWithPoint($deviceId);
            $totalNo = $this->checkBalance($deviceId);
            $user->total=$total;
            $user->refil_amount=$add_amountWithoutPoint;
            $user->last_balance = $totalNo-$add_amountWithoutPoint;
            $user->created_at = $date;
            if($user != null){                
                    $email = $user->email;
                    $total = $this->checkBalanceWithPoint($user->id);//check customer balance
                    $user->total = $total;
                    $name=ucfirst($user->name);
                    $band = date('d/m/y H:i',strtotime($user->issued_at));
                $msg = "​Dear $name, $add_amount credited to your band on $date. Available balance is $total. Regards TCL.";
                    $urlSMS = 'www.mgage.solutions/SendSMS/sendmsg.php?uname=shalinitcl&pass=welcome1&send=THETCL&dest='.$user->contact_number.'&msg='.str_replace(' ','%20',$msg);
                    $this->sendSMS($urlSMS);//send to function
                if ($email != null or $email != "") {
                    $a=Mail::send('emails.refill_wallet',['user'=>$user],function($message) use ($email) {
                        $message->to($email,$email)->subject('Wallet successfully recharged'); //send mail to the users for register link
                    });
                }
            }
        }

        /**
         * Send sms to user
         *
         * @param  URL
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function sendSMS($url)
        {
                $ch = curl_init();  
                curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            //  curl_setopt($ch,CURLOPT_HEADER, false); 
                $output=curl_exec($ch);
                curl_close($ch);
                return $output;
        }
        /**
         * Edit user
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function userEdit(Request $request)
        {
            $input = $request->all();
                    $this->validate($request,[
                        'name'      => 'required',
                        'email'     => 'required|email|unique:users,email,'.@$input['user_id'],
                        'mobile'    => 'required',
                    ]);
                    try {
                            DB::beginTransaction();
                            User::where('id',$input['user_id'])->update([
                                'name'  => $input['name'],
                                'email' => $input['email'],
                                'mobile' => $input['mobile'],
                            ]);
                            DB::commit();
                    } catch (\Exception $error) {
                        DB::rollBack();
                        return response()->error('Internal server error.');
                    }
                    return response()->success(['customer' => User::find($input['user_id'])]);
        }
        /**
         * Check user pin already exists or not
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function checkUserPin(Request $request)
        {

            $input = $request->all();
            try{
                $check = CustomerDevices::where('customer_id',$input['customer_id'])->whereNotNull('pin')->first();
                    if (count($check) > 0) {
                        $checkPin = Hash::check($input['pin'],$check->pin);
                        if($checkPin){
                        return Response::json(array(
                                'success' => true,
                                'status_code' => 201,
                                'message'   => 'PIN found',
                            ));   
                        }else{
                            return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message'   => 'PIN not found',
                    ));   
                        }
                    }else{
                         return Response::json(array(
                        'success' => false,
                        'status_code' => 500,
                        'message'   => 'Customer not found',
                    ));
                }
            }catch(\Exception $error){
                return Response::json(array(
                    'success' => false,
                    'status_code' => 500,
                    'message'   => 'Internal server error',
                ));
            }
        }

        /**
         * update status of the user band is lost or damaged
         *
         * @param  customerId , status
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function ReportBand(Request $request)
        {
                $input = $request->all();
                $this->validate($request,[
                    'status'      => 'required',
                ]);
                try{

                    $devicePoolData = CustomerDevices::where('customer_id',$input['customer_id'])->first();

                    if(count($devicePoolData) > 0){
                        $devicePoolId = $devicePoolData->device_pool_id;

                        $update = DevicePool::where('id',$devicePoolId)->update([
                            'status' => $input['status'],
                        ]);
                        //if (isset($input['comment']) and !empty($input['comment'])) {
                            $updatecom = CustomerDevices::where('customer_id',$input['customer_id'])->update([
                                'comment' => isset($input['comment'])?$input['comment']:null,
                                'is_active' => 0,
                            ]);
                       // }
                        if ($update) {
                            return Response::json(array(
                                        'success' => true,
                                        'status_code' => 201,
                                        'message'   => 'UUID Inactivated',
                                    ));
                        }else{
                            return Response::json(array(
                                        'success' => false,
                                        'status_code' => 500,
                                        'message'   => 'Device not found',
                                    ));
                        }

                    }else{
                        return Response::json(array(
                                        'success' => false,
                                        'status_code' => 500,
                                        'message'   => 'Customer`s device not found',
                                    ));
                    }
                }catch(\Exception $error){
                    return Response::json(array(
                                        'success' => false,
                                        'status_code' => 500,
                                        'message'   => 'internal server error',
                                    ));
                }

        }
        /**
         * add mobile_id to user table
         *
         * @param  customerId , status
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function addMobileId(Request $request)
        {
            $input = $request->all();
            try{
                \DB::beginTransaction();
                User::where('id',\Auth::user()->id)->update([
                        'mobile_id' => $input['mobile_id'],
                ]);
                \DB::commit();
            }catch(\Exception $error){
                \DB::rollBack();
                return Response::json(array(
                                        'success' => false,
                                        'status_code' => 500,
                                        'message'   => 'internal server error',
                                    ));
            }
            return Response::json(array(
                                        'success' => true,
                                        'status_code' => 201,
                                        'message'   => 'Device Token Added successfully',
                                    ));
        }
        /**
         * return band
         *
         * @param  original_UUID
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
        public function bandReturn(Request $request)
        {
            $this->validate($request,[
                'original_UUID'    => 'required',
            ]);
            try {
                \DB::beginTransaction();
            $input = $request->all();
                $deviceDelete = CustomerDevices::where('UUID',$input['original_UUID'])->whereNull('deleted_at')->first();
                $credit = 0;
                $debit = 0;
                    $customerRecord = CustomerWalletHistory::where('customer_device_id',$deviceDelete->id)->get();
                                if (count($customerRecord) >0 ) {
                                    foreach ($customerRecord as $key => $wallet) {
                                        $credit += $wallet->credit_amount;
                                        $debit += $wallet->debit_amount;
                                    }
                                    $total = $credit - $debit;
                                }else{
                                    $total = 0;
                                }

                if($deviceDelete){
                    $deviceDelete->is_active = 0;
                    $deviceDelete->save();
                    $delete = $deviceDelete->delete();
                    // @author: Ritu Slaria, Added check if total > 0
                    if($delete){
                        if ($total > 0) {
                            CustomerWalletHistory::create([
                            'debit_amount'  => $total,
                            'credit_amount' => null,
                            'customer_device_id'    => $deviceDelete->id,
                            'comment'      => 'Band returned',
                        ]);    
                        }
                        
                        DevicePool::where('original_UUID',$input['original_UUID'])->update(['status'=>1]);
                    }
                    
                }
                else{
                    return Response::json(array(
                                    'success' => false,
                                    'status_code' => 500,
                                    'message'   => 'Device not found',
                                ));
                }
            \DB::commit();
            }catch(\Exception $error){
                \DB::rollBack();
                return Response::json(array(
                                        'success' => false,
                                        'status_code' => 500,
                                        'message'   => 'Internal server error',
                                    ));
            }
            return Response::json(array(
                                        'success' => true,
                                        'status_code' => 201,
                                        'message'   => 'Device deleted successfully',
                                    ));
        }
}
