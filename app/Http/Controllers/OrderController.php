<?php
/**
 * @package App/Http/Controllers
 *
 * @class OrderController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;

use App\Category;
use App\Country;
use App\Customer;
use App\DiscountOfferRule;
use App\Employee;
use App\Franchise;
use App\Http\Requests\OrderRequest;
use App\InactiveMenuItems;
use App\Menu;
use App\NcOrder;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\ProductPrice;
use App\SpecialProduct;
use App\Tax;
use App\User;
use App\DeviceType;
use App\CustomerWalletHistory;
use App\DevicePool;
use App\CustomerDevices;
use App\ManageTable;
use Carbon\Carbon;
use DB;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @package App/Http/Controllers
     *
     * @class OrderController
     *
     * @method index 
     *
     * @author Parth Patel <parth.d9ithub@gmail.com>
     *
     * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
     */
    public function index()
    {
        $orderNumber = Input::get('order');
        $orderDate = Input::get('date');

        $franchiseId= Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
        $employees = Employee::whereFranchiseId($franchiseId)->pluck('user_id')->toArray();
        if (isset($orderNumber) && isset($orderDate)) {
            $order = Order::whereOrderNumber($orderNumber)->whereCreatedAt($orderDate)
                    ->whereIn('order_taken_by', $employees)->orderBy('orders.created_at', 'ASC')->get();
        } else {
            if (Auth::user()->role_name == '5' || Auth::user()->role_name == '2' || Auth::user()->role_name == '4') {
                $order = Order::with(['orderDetail' => function ($query) {
            $query->select('*')
            ->where('removed_by', null);
        }, 'orderDetail.product','customer', 'employee.user'])->whereIn('order_taken_by', $employees)
                    ->where('orders.status', '!=', 'delivered')
                    ->where('orders.status', '!=', 'cancelled')->orderBy('orders.created_at', 'DESC')->get();
            } else {
                $order = Order::with(['orderDetail' => function ($query) {
            $query->select('*')
            ->where('removed_by', null);
        }, 'orderDetail.product','customer', 'employee.user'])->where('order_taken_by', Auth::user()->id)->where('orders.status', '!=', 'delivered')
                    ->where('orders.status', '!=', 'cancelled')->orderBy('orders.created_at', 'DESC')->get();
            }

        }

        // develop by Parth Patel <parth.d9ithub@gmail.com>
        foreach ($order as $key => $value) {
            if ($value->status == 'ready') {
                $value['status'] = 'Ready to serve';
            }else{
                $value['status'] = ucfirst($value->status);
            }
            if($value->payment_method == '1'){
                $value['payment_method'] = 'Cash';   
            }elseif($value->payment_method == '2'){
                $value['payment_method'] = 'Card';   
            }elseif($value->payment_method == '3'){
                $value['payment_method'] = 'PayTM';   
            }elseif($value->payment_method == '4'){
                $value['payment_method'] = 'Wallet';   
            }
            $value['role_name'] = Auth::user()->role_name;
            $value['user_id'] = Auth::user()->id;
            $tables = ManageTable::where('id',$value->table_id)->first();
            $value['table_no'] = $tables->name;
        }

        return response()->success(['orders' => $order]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'customer_id'   => 'sometimes|required',
            'order.*.product_id' => 'sometimes|required|exists:products,id',
            'order.*.quantity' => 'sometimes|required|numeric',
            'order.*.reason'    => 'sometimes|required',
        ];
        $validator =  \Illuminate\Support\Facades\Validator::make($input, $rule);
        if ($validator->fails()) {
            return Response::json(array(
                'error' => true,
                'status_code' => 422,
                'message' => $validator->messages(),
            ));
        } else {
            $discount='';
            try {
                \DB::beginTransaction();
                if($input['payment'] == 4){
                    $input['cash_given'] = null;
                    $deviceData = CustomerDevices::where('customer_id',$input['customer_id'])->where('is_active',1)->first();
                    if (count($deviceData) > 0) {
                        $total = $this->checkBalanceOrder($deviceData->id);
                        if ($input['grand_total'] > $total) {
                            return Response::json(array(
                                'error' => true,
                                'status_code' => 500,
                                'message' => 'Wallet balance is lower than grand total.',
                            ));
                        }
                    }else{
                        return Response::json(array(
                                'error' => true,
                                'status_code' => 500,
                                'message' => 'Customer device not found.',
                            ));
                    }
                }
                $franchiseId= Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
                $taxes = Tax::whereFranchiseId($franchiseId)->pluck('tax_rate', 'tax_name')->toArray();
                if ($input['customer_id'] == 'newUser') {
                    $customer = Customer::create([
                        'name' => $input['customer']['display'],
                        'email' => $input['customer']['email'],
                        'contact_number' => $input['customer']['contact_number']
                    ]);
                    $order = Order::create([
                        'customer_id'   => $customer->id,
                        'order_number'  => (new Order())->getNewOrderNumber(),
                        'payment_method' => isset($input['payment'])?$input['payment']:null,
                        'order_taken_by' => Auth::user()->id,
                        'transaction_id' => $input['transaction_id'],
                        'created_at'    => isset($input['created_at'])?date('Y-m-d H:i:s', $input['created_at']):date('Y-m-d H:i:s'),
                        'cash_given' => isset($input['cash_given'])?$input['cash_given']:0,
                        'sub_total' => $input['sub_total'],
                        'discount' => $input['discounts'],
                        'offer' => $input['offer'],
                        'tax_collected' => $input['tax_collected'],
                        'grand_total' => $input['grand_total'],
                        'ordered_at'  =>  date('Y-m-d H:i:s'),
                        'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                        'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                        'card_number' => isset($input['card_number'])?$input['card_number']:null,
                    ]);
                } else {
                    $order = Order::create([
                        'customer_id'   => $input['customer_id'],
                        'order_number'  => (new Order())->getNewOrderNumber(),
                        'payment_method' => isset($input['payment'])?$input['payment']:null,
                        'order_taken_by' => Auth::user()->id,
                        'transaction_id' => $input['transaction_id'],
                        'created_at'    => isset($input['created_at'])?date('Y-m-d H:i:s', $input['created_at']):date('Y-m-d H:i:s'),
                        'cash_given' => isset($input['cash_given'])?$input['cash_given']:0,
                        'sub_total' => $input['sub_total'],
                        'discount' => $input['discounts'],
                        'offer' => $input['offer'],
                        'tax_collected' => $input['tax_collected'],
                        'grand_total' => $input['grand_total'],
                        'ordered_at'  =>  date('Y-m-d H:i:s'),
                        'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                        'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                        'card_number' => isset($input['card_number'])?$input['card_number']:null,
                    ]);
                }
                if (isset($input['nc'])) {
                    NcOrder::create([
                        'order_id' => $order->id,
                        'non_chargeable_people_id' => $input['nc_id'],
                        'comment'   => $input['nc_comment']
                    ]);
                }
                $productId = $quantity = $orderDetails = [];
                foreach ($input['order'] as $orderDetail) {
                    $productId = $orderDetail['product_id'];
                    $quantity = $orderDetail['quantity'];
                    $discountAmount = 0;
                    $created_at = date('Y-m-d', strtotime($order['created_at']));
                    //$discounts = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                    $categories = [];
                    $menuProducts = Menu::pluck('product_id')->toArray();
                        if (in_array($productId, $menuProducts)) {
                            $categories = Menu::where('product_id', $productId)->first()->category_id;
                        }
                    $offers = DiscountOfferRule::whereRuleType('offer')->where('is_active', '1')
                        ->where(function ($query) use($created_at) {
                            $query->where('from_date', '<=', $created_at)->where('to_date', '>=', $created_at);
                        })->get()->toArray();
                    $offerAmount = 0;
                    if (!empty($offers)) {
                        $offerDiscount = 0;
                        foreach ($offers as $offer) {
                            $amount = $offer['amount'];
                            $discountQty = $offer['discount_qty_step'];
                            $orderProducts = $discountQty+round($offer['amount']);
                            $amountType = $offer['amount_type'];
                            $condition = json_decode($offer['conditions'], true);
                            $type = $condition['type'];
                            if ($type == 'products') {
                                $id = $condition['ids'];
                               // $productExists = ;
                                if (!in_array($productId, $id)) {
                                    $offerDiscount = 0;
                                } else {
                                    if ($amountType == 'fixed') {
                                        $offerDiscount = $amount;
                                    } elseif ($amountType == 'percent') {
                                        $offerDiscount = ($orderDetail['base_price'] * $amount) / 100;
                                    } else {
                                        for ($q = 1; $q < 2000; $q++) {
                                            if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                                $offerDiscount += ($orderDetail['base_price'] * $amount);
                                            }
                                        }
                                    }
                                }
                            } elseif ($type == 'all') {
                                if ($amountType == 'fixed') {
                                    $offerDiscount = $amount;
                                } elseif ($amountType == 'percent') {
                                    $offerDiscount = ($orderDetail['price'] * $amount) / 100;
                                } else {
                                    for ($q = 1; $q < 2000; $q++) {
                                        if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                            $offerDiscount += ($orderDetail['base_price'] * $amount);
                                        }
                                    }
                                }
                            } else {
                                $id = $condition['ids'];
                               // $categoryExists = !in_array($categories, $id);

                                if (!in_array($categories, $id)) {
                                    $offerDiscount = 0;
                                } else {
                                    if ($amountType == 'fixed') {
                                        $offerDiscount = $amount;
                                    } elseif ($amountType == 'percent') {
                                        $offerDiscount = ($orderDetail['base_price'] * $amount) / 100;
                                    } else {
                                        for ($q = 1; $q < 2000; $q++) {
                                            if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                                $offerDiscount += ($orderDetail['base_price'] * $amount);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $offerAmount = $offerDiscount;
                    }
                    //$offerAmount = offerAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                    if($offerAmount){
                        $offerAmount = $offerAmount;
                        $discountAmount = 0;
                    } else {
                        $offerAmount = 0;
                        if (isset($input['discount_type'])){
                            if($input['discount_type'] == 'fixed'){
                                $discountAmount = $input['discount_amount'];
                            } else {
                                $discountAmount = (($orderDetail['price']*$input['discount_amount']) / 100);
                            }
                        } else {
                             $discounts = DiscountOfferRule::whereRuleType('discount')->where('is_active', '1')
                            ->where(function ($query) use($created_at) {
                                $query->where('from_date', '<=', $created_at)->where('to_date', '>=', $created_at);
                            })->get()->toArray();
                            if (!empty($discounts)) {
                                $discountAmounts = 0;
                                foreach ($discounts as $discount) {
                                    $amount = $discount['amount'];
                                    $amountType = $discount['amount_type'];
                                    $condition = json_decode($discount['conditions'], true);
                                    $type = $condition['type'];
                                    if ($type == 'products') {
                                        $id = $condition['ids'];

                                        if (!in_array($productId, $id)) {
                                            $discountAmounts=0;
                                        } else {
                                            if ($amountType == 'fixed') {
                                                if ($orderDetail['price'] >= $amount) {
                                                        $discountAmounts = $amount* $orderDetail['quantity'];
                                                  }
                                            } else {
                                                $discountAmounts = ($orderDetail['price']*$amount)/100;
                                            }
                                        }
                                    } elseif ($type =='all') {
                                        if ($amountType == 'fixed') {
                                             if ($orderDetail['price'] >= $amount) {
                                                        $discountAmounts = $amount* $orderDetail['quantity'];
                                                  }
                                        } else {
                                            $discountAmounts = ($orderDetail['price']*$amount)/100;
                                        }
                                    } else {
                                        $id = $condition['ids'];

                                        if (!in_array($categories, $id)) {
                                            $discountAmounts=0;
                                        } else {
                                            if ($amountType == 'fixed') {
                                               if ($orderDetail['price'] >= $amount) {
                                                        $discountAmounts = $amount* $orderDetail['quantity'];
                                                  }
                                            } else {
                                                $discountAmounts = ($orderDetail['price']*$amount)/100;
                                            }
                                        }
                                    }
                                }
                                $discountAmount = $discountAmounts;
                            }
                        }    
                    }
                    //$discount = $discounts + $discountAmount;

                    $totalPrices = $orderDetail['price'] - $discountAmount -$offerAmount;
                    $serviceTax = 0;
                    if (isset($input['service_tax'])) {
                        $serviceTax = round(($totalPrices * $input['service_tax']) / 100, 2);
                    }
                    /*$serviceCharge = $vat = $serviceTax = $serviceChargeAmount =  $vatAmount = $serviceTaxAmount = 0;*/
                    $cgstTax = $sgstTax = 0;
                    /* if (isset($taxes['Service Charge'])) {
                         $serviceCharge = round(($totalPrices * $taxes['Service Charge']) / 100, 2);
                     }
                     $subTotal = $totalPrices + $serviceCharge;
                     if (isset($taxes['VAT'])) {
                         $vat = round(($subTotal * $taxes['VAT']) / 100, 2);
                     }
                     if (isset($taxes['Service Tax'])) {
                         $serviceTax = round(($subTotal * $taxes['Service Tax']) / 100, 2);
                     }*/
                    /*if (isset($taxes['SGST'])) {
                        $sgstTax = round(($totalPrices * $taxes['SGST']) / 100, 2);
                    }
                    if (isset($taxes['CGST'])) {
                        $cgstTax = round(($totalPrices * $taxes['CGST']) / 100, 2);
                    }
                    $billAmount = round($totalPrices + $cgstTax + $sgstTax);*/
                    $taxRate = round((($totalPrices+$serviceTax) * $orderDetail['tax_rate']) / 100, 2);
                    $billAmount = round((($totalPrices+$serviceTax) + $taxRate), 2);
                    $orderDetails[] = OrderDetail::create([
                        'product_id' => $orderDetail['product_id'],
                        'order_id'   => $order->id,
                        'quantity'   => $orderDetail['quantity'],
                        'sub_total'  => $orderDetail['price'],
                        'discount' => $discountAmount,
                        'offer' => $offerAmount,
                        'tax_collected' => $taxRate,
                        'grand_total' => $billAmount,
                        'is_product_variant' => 0,
                    ]);
                }
                $order['order'] = $orderDetails;
                Log::useDailyFiles(public_path().'/logs/orders.log');
                Log::info('Order Place Successfully: '.$order);
                $customer_device = DB::table('customer_device')->where('customer_id',$input['customer_id'])->where('is_active',1)->first();
                if (count($customer_device) > 0 and $input['payment'] == 4) {
                    $customerAddWallet = CustomerWalletHistory::create([
                        'customer_device_id'    => $customer_device->id,
                        'debit_amount'          => $order->grand_total,
                    ]);
                }
                if (!isset($input['nc'])) {
                    $this->SendMailAndSMSOrderCreate($order);
                }//send mail to the customer
                \DB::commit();
            } catch (\Exception $error) {
                \DB::rollBack();
                Log::useDailyFiles(public_path().'/logs/errors.log');
                Log::error('Error: '.$error->getMessage());
                return response()->error(['message' => $error->getMessage()]);
            }
            return response()->success(['message' => 'Order Place Successfully', 'orders' => $order]);
        }

    }


    /**
     * send Mail and SMS to the user for order create
     *
     * @param  customerId
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    
    public function SendMailAndSMSOrderCreate($order)
    {
        $user=DB::table('customers')
                    ->where('id',$order->customer_id)
                    ->first();
        $user->order_number = $order->order_number;
        $user->order_date = $order->created_at;
        $method = \Config::get('constants.PaymentMethod');
        $user->payment_method = $method[$order->payment_method];
        $subTotal = 0;
        $taxTotal = 0;
         $taxRatesTotal = 0;
        $taxArray =array();
        $taxArrayData =array();
        $rates = 0;
        $subTotals = $order->sub_total - $order->discount - $order->offer;
        $orderDetails = array();
        $tex_string = [];
        foreach ($order['order'] as $key => $value) {
            $productName=Product::find($value->product_id);
            $orderDetails[$key]['product_name'] = $productName->name;
            $orderDetails[$key]['quantity'] = $value->quantity;
            $orderDetails[$key]['unit_price'] = round($value['sub_total']/$value->quantity, 2);
            $orderDetails[$key]['offer'] = $value->offer;
            if($order->discount > 0){
                $orderDetails[$key]['discount'] = $value->discount;
            }else{
                $orderDetails[$key]['discount'] = null;
            }
            //$orderDetails[$key]['tax_collected'] = $value->tax_collected;
            $orderDetails[$key]['amount'] = $value['sub_total'];
            if($productName->tax_id == 0){
                if ($productName->menu->categoryName->tax_id != 0) {
                    $nameTax[0] = $productName->menu->categoryName->tax->tax_name;//taxName
                    $nameTax[1] = $productName->menu->categoryName->tax->tax_rate;//taxRate
                    $nameTax[2] = $productName->menu->categoryName->tax->id;//taxRate
                    $nameTax[3] = $productName->menu->categoryName->tax->tax_type;//taxType                    
                }else{
                        $catId = $productName->menu->category_id;
                        $nameTax = $this->Recursive($catId);
                }
                $orderDetails[$key]['tax_name'] = $nameTax[0];
                $orderDetails[$key]['rate'] = $nameTax[1];
                $orderDetails[$key]['tax_id'] = $nameTax[2];
                $orderDetails[$key]['tax_type'] = $nameTax[3];
            }else{
                $orderDetails[$key]['tax_name'] = $productName->tax->tax_name;   
                $orderDetails[$key]['rate'] = $productName->tax->tax_rate;
                $orderDetails[$key]['tax_id'] = $productName->tax->id;
                $orderDetails[$key]['tax_type'] = $productName->tax->tax_type;
            }        
             $rate = \Config::get('constants.TAX_TYPE');
             $serviceTax = \App\Tax::where('tax_type',2)->first();
            if($orderDetails[$key]['discount'] != null){
                $productPricesDiscount = $orderDetails[$key]['discount'];
            }else{
                $productPricesDiscount = 0;
            }
            if($orderDetails[$key]['offer'] != 0){
                $productPricesOffer = $orderDetails[$key]['offer'];
            }else{
                $productPricesOffer = 0;
            }

            if (isset($serviceTax) and $serviceTax != null) {
                $user->serviceTax = round($serviceTax->tax_rate).'% '.$serviceTax->tax_name.' = '.(round($subTotals*$serviceTax->tax_rate/100, 2));
                $serviceTaxOnPrice = ($orderDetails[$key]['amount']-$productPricesDiscount-$productPricesOffer)*$serviceTax->tax_rate/100;
            }else{
                $user->serviceTax = '';
                $serviceTaxOnPrice = 0;
            }
            $affterDiscountSubTotal = ($orderDetails[$key]['amount']-$productPricesDiscount-$productPricesOffer)+$serviceTaxOnPrice;
            $allTaxRate = $affterDiscountSubTotal*$orderDetails[$key]['rate']/100;
             /*if ($orderDetails[$key]['tax_type'] == 3) {
                 $tex_string = array('tax_id'=>$orderDetails[$key]['tax_id'],
                                'rate'=>$orderDetails[$key]['rate'],
                                'tax_type'=>$rate[$orderDetails[$key]['tax_type']],
                                'tax_name'=>$orderDetails[$key]['tax_name'],
                                'tax_rate'=>$affterDiscountSubTotal*$orderDetails[$key]['rate']/100);
             }else{
               
             }*/
              /*$taxRatesTotal += ($affterDiscountSubTotal*$orderDetails[$key]['rate']/100);
                $tex_string = array('tax_id'=>$orderDetails[$key]['tax_id'],
                                'rate'=>$orderDetails[$key]['rate'],
                                'tax_type'=>$rate[$orderDetails[$key]['tax_type']],
                                'tax_name'=>$orderDetails[$key]['tax_name'],
                                'tax_rate'=> $taxRatesTotal,
                            );
             
             if(!in_array($tex_string['tax_type'],$taxArray)){
                $taxArray[] = $tex_string['tax_type'];
                $taxTotal += $orderDetails[$key]['rate'];
                $rates += $tex_string['tax_rate'];  
                $taxArrayData[] = $tex_string;
             }else{
                for ($i=0; $i < count($taxArrayData); $i++) { 
                $pos = array_search($tex_string['tax_type'], $taxArrayData[$i]);
                if ($pos) {
                    unset($taxArrayData[$i]);
                   $taxArrayData[$i] = $tex_string; 
                }
                }
             }*/
             if (array_key_exists($orderDetails[$key]['tax_name'], $taxArrayData)) {
                $taxArrayData[$orderDetails[$key]['tax_name']]['rate'] += $allTaxRate;
                $taxArrayData[$orderDetails[$key]['tax_name']]['type'] = $rate[$orderDetails[$key]['tax_type']];
                $taxArrayData[$orderDetails[$key]['tax_name']]['tax_rate'] = $orderDetails[$key]['rate'];
            } else {
                $taxArrayData[$orderDetails[$key]['tax_name']]['rate'] = $allTaxRate;
                $taxArrayData[$orderDetails[$key]['tax_name']]['type'] = $rate[$orderDetails[$key]['tax_type']];
                $taxArrayData[$orderDetails[$key]['tax_name']]['tax_rate'] = $orderDetails[$key]['rate'];
            }
            
        }
       // dd($taxArrayData);
        $user->transactionId = $order['transaction_id'];
        $user->taxData = $taxArrayData;
        $user->taxTotal = $order['tax_collected'];
        $user->subTotal = $order->sub_total;
        $user->discount = $order->discount;
        $user->offer = $order->offer;
        $user->tax = $taxTotal;
        $user->grandTotal = $order->grand_total;
        $discountPrice = $order->sub_total - $order->discount;
        $user->TaxtAmount = $discountPrice * $user->taxTotal /100;
        $payment_methods = $rate = \Config::get('constants.PaymentMethod');
        $user->payment_method = $payment_methods[$order->payment_method];
        //send SMS 
        $this->sendOrderSMS(ucfirst($user->name),$user->contact_number,$user->order_number,$user->grandTotal,$order->transaction_id,$order->customer_id,$user->order_date,$order->payment_method);

        if($user != null){
                $email = $user->email;
            if ($email != null or $email != "") {
            $a=Mail::send('emails.order_place',['user'=>$user,'orderDetails'=>$orderDetails],function($message) use ($email,$user) {
                    $message->to($email,$email)->subject('Your Order of '.$user->order_number.' successfully paid'); //send mail to the users for register link
                });
            }
        }
    }
    /**
         * recursive function to get product tax and value
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
    public function Recursive($catId){
                            $cate = Category::where('id',$catId)->first();
                            if($cate->parent_id == 0 and $cate->tax_id != 0){
                                $taxName[0] = $cate->tax->tax_name;
                                $taxName[1] = $cate->tax->tax_rate;
                                $taxName[2] = $cate->tax->id;
                                $taxName[3] = $cate->tax->tax_type;
                                return $taxName;
                            }
                            else{
                                return $this->Recursive($cate->parent_id);
                            }
    }
    /**
         * Send SMS to the user
         *
         * @param  customerId
         * @return \Illuminate\Http\Response
         * @author Parth Patel <parth.d9ithub@gmail.com>
         */
    public function sendOrderSMS($name,$contactNo,$orderNo,$paidAmount,$transactinId,$customerId,$orderDate,$paymentMethod)
    {   
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $orderDate, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
        if($paymentMethod == 4){   
            $device = CustomerDevices::where('customer_id',$customerId)->first();
            $total = $this->checkBalance($device->id);//check customer balance
            $sms_content = "Dear $name, your order of $orderNo has been successfully ​paid on ​$date. Transaction Id: $transactinId. Available balance ​is $total. Regards TCL.";
            /*$sms_content = 'Dear'.$name.'your order '.$orderNo.' of '.$paidAmount.' has been successfully Paid on '.$date.'. Transaction Id: '.$transactinId.'. Available balance ​is '.$amount.'.';*/
        }else{
            $sms_content = "Dear $name, your order of $orderNo has been successfully ​paid on ​$date. Transaction Id: $transactinId. Regards TCL.";
        }
           $url = 'www.mgage.solutions/SendSMS/sendmsg.php?uname=shalinitcl&pass=welcome1&send=THETCL&dest='.$contactNo.'&msg='.str_replace(' ','%20',$sms_content);
            $ch = curl_init();  
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //  curl_setopt($ch,CURLOPT_HEADER, false); 
            $output=curl_exec($ch);
            curl_close($ch);
            return $output;
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
            return number_format($total,2);
    }
    /**
     * get customer wallet balance
     *   
     * @author Parth Patel <parth.d9ithub@gmail.com>
     * @param  deviceId(customer's device id)
     * @return \Illuminate\Http\Response
     */

    public function checkBalanceOrder($deviceId)
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
     * Display the specified resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function show($orderId)
    {
        $orders =  Order::with(['orderDetail' => function ($query) {
            $query->select('*')
            ->where('removed_by', null);
        }, 'ncOrder'])->whereId($orderId)->get()->toArray();
        $customer = Customer::pluck('name', 'id')->toArray();
        $productName = Product::pluck('name', 'id')->toArray();
        $productId = $quantity = $viewOrder = $detail = [];
        $orderTakenBy = User::whereId(\Auth::user()->id)->first()->name;
        $productTotal = [];
        $franchises= \App\Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
        $taxes = \App\Tax::whereFranchiseId($franchises)->pluck('tax_rate', 'tax_name')->toArray();
        $storeMangerEmail = '';
        $franchiseData = $country = '';
        foreach ($orders as $order) {
                $franchiseId= Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
                $franchiseData = Franchise::whereId($franchiseId)->first();
                $storeManger = Employee::whereFranchiseId($franchiseId)->pluck('user_id');
                $storeMangerEmail = User::whereRoleName('4')->whereIn('id', $storeManger)->first()->email;
                $country = Country::whereId($franchiseData->country_id)->first()->name;
            foreach ($order['order_detail'] as $orderDetail) {
                $productId[] = $orderDetail['product_id'];
                $quantity[] = $orderDetail['quantity'];
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
                $menuCategory = Menu::whereProductId($orderDetail['product_id'])->first()->category_id;
                $category = Category::whereId($menuCategory)->first();
                $product = Product::whereId($orderDetail['product_id'])->first();
                if (!empty($product->tax_id)) {
                    $taxes = Tax::whereId($product->tax_id)->first();
                    $taxRate = $taxes ? $taxes->tax_rate : null;
                    $taxName = $taxes ? $taxes->tax_name : null;
                    $taxType = $taxes ? $taxes->tax_type : null;
                } else {
                    $tax = Tax::whereId($category->tax_id)->first();
                    $taxRate = $tax ? $tax->tax_rate : null;
                    $taxName = $tax ? $tax->tax_name : null;
                    $taxType = $tax ? $tax->tax_type : null;
                }
                $productTags = [];
                if (!empty($product->productTag)) {
                    foreach ($product->productTag as $productTag) {
                        $productTags[] = [
                            'product_tag' => URL('img').'/'.$productTag->tag->tag_icon_image,
                        ];
                    }
                }
                $detail[]  = [
                    'id' => $orderDetail['id'],
                    'product_id' => $orderDetail['product_id'],
                    'offer' => $orderDetail['offer'],
                    'discount' => $orderDetail['discount'],
                    'name' => $productName[$orderDetail['product_id']],
                    'price' => $productsPrices,
                    'quantity' => $orderDetail['quantity'],
                    'tax_rate'  => $taxRate,
                    'tax_name'  => $taxName,
                    'tax_type' => $taxType,
                    'tags'      => $productTags,
                ];
                $productTotal[] = ($productsPrices * $orderDetail['quantity']);
            }
            if ($order['cash_given'] == 0) {
                $changeAmount = 0;
            } else {
                $changeAmount = $order['cash_given']-$order['grand_total'] ;
            }
            $viewOrder = [
                'id' =>  $order['id'],
                'customer_id' => $order['customer_id'],
                'customer_name' => $customer[$order['customer_id']],
                'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $order['created_at'], 'UTC')->setTimezone('Asia/Kolkata')->format('Y-n-j H:i:s'),
                'order_number' => $order['order_number'],
                'order'  => $detail,
                'order_taken_by' => $orderTakenBy,
                'payment' => $order['payment_method'],
                'franchise_name' => $franchiseData->name,
                'address_line_one' => $franchiseData->address_line_one,
                'address_line_two' => $franchiseData->address_line_two,
                'city' => $franchiseData->city,
                'region' => $franchiseData->region,
                'country' => $country,
                'transaction_id' => $order['transaction_id'],
                'store_manager_email' => $storeMangerEmail,
                'cash_given' => $order['cash_given'],
                'subtotal' => $order['sub_total'],
                'discount' => $order['discount'],
                'offer' => $order['offer'],
                'tax_collected' => $order['tax_collected'],
                'grand_total' => $order['grand_total'],
                'changeAmount' => $changeAmount,
                'gst_number'    => $franchiseData->gst_number,
                'non_chargeable_people_id' => !empty($order['nc_order'])?$order['nc_order']['non_chargeable_people_id']:null,
                 'comment'   => !empty($order['nc_order'])?$order['nc_order']['comment']:null,
                 'table_no'   =>   isset($order['table_id'])?$order['table_id']:null,
                'paytm_mobile' => isset($order['paytm_mobile'])?$order['paytm_mobile']:null,
                'card_number' => isset($order['card_number'])?$order['card_number']:null,
                /*'tax_amount'  => round($taxes['GST'], 2),
                'gst_amount'  => round($taxes['GST'], 2),
                'gst'         => $order['tax_collected']*/
            ];
        }
        $totalPrice = array_sum($productTotal);
        $viewOrder['total'] =  getTaxes($productId, $totalPrice, date('Y-m-d', strtotime($viewOrder['created_at'])));
        return response()->success(['orders' => $viewOrder]);
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
     * @param $orderId
     * @return mixed
     */
    public function update(Request $request, $orderId)
    {

        $input = $request->all();
        $rule = [
            'customer_id'   => 'sometimes|required',
            'order.*.product_id' => 'sometimes|required|exists:products,id',
            'order.*.quantity' => 'sometimes|required|numeric',
            'order.*.reason'    => 'sometimes|required',
        ];
        $validator =  \Illuminate\Support\Facades\Validator::make($input, $rule);
        if ($validator->fails()) {
            return Response::json(array(
                'error' => true,
                'status_code' => 422,
                'message' => $validator->messages(),
            ));
        } else {
            $discount = '';
            try {
                \DB::beginTransaction();
                $orderTime = Order::whereId($orderId)->first()->created_at;
                    $franchiseId = Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
                    $taxes = Tax::whereFranchiseId($franchiseId)->pluck('tax_rate', 'tax_name')->toArray();
                    $orderedProduct = OrderDetail::whereOrderId($orderId)->where('removed_by', null)
                        ->pluck('quantity', 'product_id')->toArray();
                    $productId = $quantity = $orderDetails = [];
                    foreach ($input['order'] as $orderDetail) {
                        $discountAmount = 0;
                        $created_at = date('Y-m-d', strtotime($input['created_at']));
                        /* if (isset($input['discount_type'])){
                             if($input['discount_type'] == 'fixed'){
                                 $discountAmount = $input['discount_amount'];
                             } else {
                                 $discountAmount = (($orderDetail['price']*$input['discount_amount']) / 100);
                             }
                         } else {
                             $discountAmount = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                         }*/

                        //$discounts = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                        $categories = [];
                        $offers = DiscountOfferRule::whereRuleType('offer')->where('is_active', '1')
                            ->where(function ($query) use ($created_at) {
                                $query->where('from_date', '<=', $created_at)->where('to_date', '>=', $created_at);
                            })->get()->toArray();
                        $offerAmount = '';
                        if (!empty($offers)) {
                            if (is_array($productId)) {
                                foreach ($productId as $product) {
                                    $menuProducts = Menu::pluck('product_id')->toArray();
                                    if (in_array($product, $menuProducts)) {
                                        $categories[] = Menu::where('product_id', $product)->first()->category_id;
                                    }

                                }
                            }
                            $offerDiscount = [];

                            foreach ($offers as $offer) {
                                $amount = $offer['amount'];
                                $discountQty = $offer['discount_qty_step'];
                                $orderProducts = $discountQty + round($offer['amount']);
                                $amountType = $offer['amount_type'];
                                $condition = json_decode($offer['conditions'], true);
                                $type = $condition['type'];
                                if ($type == 'products') {
                                    $id = $condition['ids'];
                                    $productExists = array_intersect($productId, $id);
                                    if (empty($productExists)) {
                                        $offerDiscount[] = '';
                                    } else {
                                        if ($amountType == 'fixed') {
                                            $offerDiscount[] = $amount;
                                        } elseif ($amountType == 'percent') {
                                            $offerDiscount[] = ($orderDetail['base_price'] * $amount) / 100;
                                        } else {
                                            for ($q = 1; $q < 2000; $q++) {
                                                if ($orderDetail['quantity'] >= ($orderProducts * $q)) {
                                                    $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                                }
                                            }
                                        }
                                    }
                                } elseif ($type == 'all') {
                                    if ($amountType == 'fixed') {
                                        $offerDiscount[] = $amount;
                                    } elseif ($amountType == 'percent') {
                                        $offerDiscount[] = ($orderDetail['price'] * $amount) / 100;
                                    } else {
                                        for ($q = 1; $q < 2000; $q++) {
                                            if ($orderDetail['quantity'] >= ($orderProducts * $q)) {
                                                $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                            }
                                        }
                                    }
                                } else {
                                    $id = $condition['ids'];
                                    $categoryExists = array_intersect($categories, $id);

                                    if (empty($categoryExists)) {
                                        $offerDiscount[] = '';
                                    } else {
                                        if ($amountType == 'fixed') {
                                            $offerDiscount[] = $amount;
                                        } elseif ($amountType == 'percent') {
                                            $offerDiscount[] = ($orderDetail['base_price'] * $amount) / 100;
                                        } else {
                                            for ($q = 1; $q < 2000; $q++) {
                                                if ($orderDetail['quantity'] >= ($orderProducts * $q)) {
                                                    $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $offerAmount = array_sum($offerDiscount);
                        }
                        //$offerAmount = offerAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                        if ($offerAmount) {
                            $offerAmount = $offerAmount;
                            $discountAmount = 0;
                        } else {
                            $offerAmount = 0;
                            if (isset($input['discount_type'])) {
                                if ($input['discount_type'] == 'fixed') {
                                    $discountAmount = $input['discount_amount'];
                                } else {
                                    $discountAmount = (($orderDetail['price'] * $input['discount_amount']) / 100);
                                }
                            } else {
                                $discountAmount = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                            }
                        }
                        //$discount = $discounts + $discountAmount;
                        $totalPrices = $orderDetail['price'] - $discountAmount - $offerAmount;
                        $serviceTax = 0;
                        if (isset($input['service_tax'])) {
                            $serviceTax = round(($totalPrices * $input['service_tax']) / 100, 2);
                        }
                        /*$serviceCharge = $vat = $serviceTax = $serviceChargeAmount =  $vatAmount = $serviceTaxAmount = 0;*/
                        $cgstTax = $sgstTax = 0;
                        /*if (isset($taxes['Service Charge'])) {
                            $serviceCharge = round(($totalPrices * $taxes['Service Charge']) / 100, 2);
                        }
                        $subTotal = $totalPrices + $serviceCharge;
                        if (isset($taxes['VAT'])) {
                            $vat = round(($subTotal * $taxes['VAT']) / 100, 2);
                        }
                        if (isset($taxes['Service Tax'])) {
                            $serviceTax = round(($subTotal * $taxes['Service Tax']) / 100, 2);
                        }
                        if (isset($taxes['GST'])) {
                            $gstTax = round(($subTotal * $taxes['GST']) / 100, 2);
                        }*/
                        $taxRate = round((($totalPrices+$serviceTax) * $orderDetail['tax_rate']) / 100, 2);
                        $billAmount = round(($totalPrices+$serviceTax) + $taxRate);
                        if (isset($orderDetail['product_id'])) {
                            if (array_key_exists($orderDetail['product_id'], $orderedProduct)) {
                                if (isset($orderDetail['quantity'])) {
                                    $productId[] = $orderDetail['product_id'];
                                    $quantity[] = $orderDetail['quantity'];
                                    //quantity change
                                    if ($orderedProduct[$orderDetail['product_id']] != $orderDetail['quantity']) {
                                        $password = $this->checkPassword($orderDetail['password']);
                                        if ($password) {
                                            Order::whereId($orderId)
                                                ->update([
                                                    'payment_method' => isset($input['payment']) ? $input['payment'] : null,
                                                    'cash_given' => isset($input['cash_given']) ? $input['cash_given'] : 0,
                                                    'sub_total' => $input['sub_total'],
                                                    'discount' => $input['discounts'],
                                                    'offer' => $input['offer'],
                                                    'tax_collected' => $input['tax_collected'],
                                                    'grand_total' => $input['grand_total'],
                                                    'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                                                    'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                                                    'card_number' => isset($input['card_number'])?$input['card_number']:null,
                                                                            ]);
                                            OrderDetail::whereOrderId($orderId)->whereProductId($orderDetail['product_id'])
                                                ->update([
                                                    'removed_by' => Auth::user()->id,
                                                    'remove_reason' => $orderDetail['reason']
                                                ]);
                                            $orderDetails[] = OrderDetail::create([
                                                'product_id' => $orderDetail['product_id'],
                                                'order_id' => $orderId,
                                                'quantity' => $orderDetail['quantity'],
                                                'sub_total' => $orderDetail['price'],
                                                'discount' => $discountAmount,
                                                'offer' => $offerAmount,
                                                'tax_collected' => $taxRate,
                                                'grand_total' => $billAmount,
                                                'is_product_variant' => 0,
                                            ]);
                                        }
                                    } else {
                                        Order::whereId($orderId)
                                                ->update([
                                                    'payment_method' => isset($input['payment']) ? $input['payment'] : null,
                                                    'cash_given' => isset($input['cash_given']) ? $input['cash_given'] : 0,
                                                    'sub_total' => $input['sub_total'],
                                                    'discount' => $input['discounts'],
                                                    'offer' => $input['offer'],
                                                    'tax_collected' => $input['tax_collected'],
                                                    'grand_total' => $input['grand_total'],
                                                    'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                                                    'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                                                    'card_number' => isset($input['card_number'])?$input['card_number']:null,
                                                                            ]);
                                            OrderDetail::whereOrderId($orderId)->whereProductId($orderDetail['product_id'])
                                                ->update([
                                                'sub_total' => $orderDetail['price'],
                                                'discount' => $discountAmount,
                                                'offer' => $offerAmount,
                                                'tax_collected' => $taxRate,
                                                'grand_total' => $billAmount,
                                                'is_product_variant' => 0,
                                            ]);
                                    }
                                } else {
                                    if (isset($orderDetail['reason'])) {
                                        $password = $this->checkPassword($orderDetail['password']);
                                        if ($password) {
                                            Order::whereId($orderId)
                                                ->update([
                                                    'payment_method' => isset($input['payment']) ? $input['payment'] : null,
                                                    'cash_given' => isset($input['cash_given']) ? $input['cash_given'] : 0,
                                                    'sub_total' => $input['sub_total'],
                                                    'discount' => $input['discounts'],
                                                    'offer' => $input['offer'],
                                                    'tax_collected' => $input['tax_collected'],
                                                    'grand_total' => $input['grand_total'],
                                                    'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                                                    'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                                                    'card_number' => isset($input['card_number'])?$input['card_number']:null,
                                                ]);
                                            OrderDetail::whereOrderId($orderId)->whereProductId($orderDetail['product_id'])
                                                ->update([
                                                    'removed_by' => Auth::user()->id,
                                                    'remove_reason' => $orderDetail['reason']
                                                ]);
                                        }
                                    }
                                }
                            } else {
                                Order::whereId($orderId)
                                    ->update([
                                        'payment_method' => isset($input['payment']) ? $input['payment'] : null,
                                        'cash_given' => isset($input['cash_given']) ? $input['cash_given'] : 0,
                                        'sub_total' => $input['sub_total'],
                                        'discount' => $input['discounts'],
                                        'offer' => $input['offer'],
                                        'tax_collected' => $input['tax_collected'],
                                        'grand_total' => $input['grand_total'],
                                        'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                                        'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                                        'card_number' => isset($input['card_number'])?$input['card_number']:null,
                                    ]);
                                $orderDetails[] = OrderDetail::create([
                                    'product_id' => $orderDetail['product_id'],
                                    'order_id' => $orderId,
                                    'quantity' => $orderDetail['quantity'],
                                    'sub_total' => $orderDetail['price'],
                                    'discount' => $discountAmount,
                                    'offer' => $offerAmount,
                                    'tax_collected' => $taxRate,
                                    'grand_total' => $billAmount,
                                    'is_product_variant' => 0,
                                ]);
                            }
                        } else {
                            if (isset($orderDetail['password'])) {
                                $password = $this->checkPassword($orderDetail['password']);
                                if ($password) {
                                    Order::whereId($orderId)
                                        ->update([
                                            'payment_method' => isset($input['payment']) ? $input['payment'] : null,
                                            'cash_given' => isset($input['cash_given']) ? $input['cash_given'] : 0,
                                            'sub_total' => $input['sub_total'],
                                            'discount' => $input['discounts'],
                                            'offer' => $input['offer'],
                                            'tax_collected' => $input['tax_collected'],
                                            'grand_total' => $input['grand_total'],
                                            'table_id'   =>   isset($input['table_no'])?$input['table_no']:null,
                        'paytm_mobile' => isset($input['paytm_mobile'])?$input['paytm_mobile']:null,
                        'card_number' => isset($input['card_number'])?$input['card_number']:null,
                                        ]);
                                    OrderDetail::whereOrderId($orderId)->where('product_id', '!=', $productId)
                                        ->whereIn('product_id', array_keys($orderedProduct))->update([
                                            'removed_by' => Auth::user()->id,
                                            'remove_reason' => $orderDetail['reason']
                                        ]);
                                }
                            }
                        }
                    }
                    $order['order_detail'] = $orderDetails;
                    if (isset($input['nc'])) {
                        $ncOrders = NcOrder::whereOrderId($orderId)->first();
                        if ($ncOrders) {
                           $ncOrders->update([
                            'non_chargeable_people_id' => $input['nc_id'],
                            'comment'   => $input['nc_comment']
                         ]);
                        } else {
                            NcOrder::create([
                            'order_id' => $orderId,
                            'non_chargeable_people_id' => $input['nc_id'],
                            'comment'   => $input['nc_comment']
                         ]);
                        }
                        
                    } else {
                        $ncOrders = NcOrder::whereOrderId($orderId)->first();
                        if ($ncOrders) {
                            $ncOrders = $ncOrders->delete();
                        }
                    }
                    /*$productPrice = ProductPrice::whereIn('product_id', $productId)->whereFranchiseId($franchiseId);
                    if (count($productPrice->get())) {
                        $productPrice = $productPrice->pluck('price')->toArray();
                    } else {
                        $productPrice = Product::whereIn('id', $productId)->pluck('price')->toArray();
                    }
                    $total = [];
                    foreach ($productPrice as $key => $price) {
                        $total[$key] = $price * $quantity[$key];
                    }
                    $totalPrice = array_sum($total);
                    $order['total'] =  getTaxes($productId, $totalPrice, date('Y-m-d', strtotime($orderTime)));*/
                \DB::commit();
            } catch (\Exception $error) {
                \DB::rollBack();
                return response()->error(['message' => $error->getMessage()]);
            }
            return response()->success(['message' => 'Successfully Updated', 'order' => $order]);
        }
    }

    /**
     * For order cancellation
     *
     * @param Request $request
     * @param int $orderId
     * @return mixed
     */
    public function orderCancellation(Request $request, $orderId)
    {
        $input = $request->all();

        try {
            $password = $this->checkPassword($input['password']);
            if ($password) {
                Order::where('status', '!=', 'cancelled')->findOrFail($orderId)->update([
                    'status' => 'cancelled',
                    'cancelled_by'   => Auth::user()->id,
                    'cancel_reason' => $input['reason']
                ]);
            } else {
                 return Response::json(array(
                'success' => true,
                'status_code' => 500,
                'message' => 'Password is incorrect'
                ));
            }
        } catch (\Exception $error) {
            return Response::json(array(
                'success' => true,
                'status_code' => 500,
                'message' => $error->getMessage()
            ));
        }
        return response()->success(['message' =>  'Deleted Successfully']);
    }

    /**
     * @return mixed
     */
    public function mostOrderedItems()
    {
        try {
            $categories = Category::whereParentId(0)->get();
            $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
            $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
            $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
            $specialProducts = SpecialProduct::where('franchise_id', $franchiseId)->pluck('product_id')->toArray();
            $franchiseProductPrice = $franchiseProductId = $menu = $popularProducts = [];
            if (!empty($productPrice)) {
                foreach ($productPrice as $price) {
                    $franchiseProductId[$price['product_id']] = $price['price'];
                    $franchiseProductPrice[] = $price['product_id'];
                }
            }
            foreach ($categories as $category) {
                if ($category->is_active == 1) {
                    if ((count($category->child)>0) || (count($category->products)>0)) {
                        foreach ($category->products as $product) {
                            //get product image
                            if ($product->is_active ==1) {
                                if (in_array($product->id, $specialProducts)) {
                                    if (!in_array($product->menu->id, $inactive)) {
                                        $productName = $product->name;
                                        if (in_array($product->id, $franchiseProductPrice)) {
                                            $productsPrice = $franchiseProductId[$product->id];
                                        } else {
                                            $productsPrice = $product->price;
                                        }
                                        if (!empty($product->tax_id)) {
                                            $taxRate = $product->tax ? $product->tax->tax_rate : null;
                                            $taxName = $product->tax ? $product->tax->tax_name : null;
                                            $taxType = $product->tax ? $product->tax->tax_type : null;
                                        } else {
                                            $taxRate = $category->tax ? $category->tax->tax_rate : null;
                                            $taxName = $category->tax ? $category->tax->tax_name : null;
                                            $taxType = $category->tax ? $category->tax->tax_type : null;
                                        }
                                        $productTags = [];
                                        if (!empty($product->productTag)) {
                                            foreach ($product->productTag as $productTag) {
                                                $productTags[] = [
                                                    'product_tag' => URL('img').'/'.$productTag->tag->tag_icon_image,
                                                ];
                                            }
                                        }
                                        $popularProducts[] = [
                                            'id' => $product->id,
                                            'name' => $productName,
                                            'price' => $productsPrice,
                                            'product_code' => $product->product_code,
                                            'base_price' => $productsPrice,
                                            'description'   => $product->description,
                                            'tax_rate'  => $taxRate,
                                            'tax_name'  => $taxName,
                                            'tags' => $productTags,
                                            'tax_type' => $taxType,
                                        ];
                                    }
                                }
                            }
                        }
                        foreach ($category->child as $subcategory) {
                            if ($subcategory->is_active == 1) {
                                if (count($subcategory->products)>0) {
                                    //get product image
                                    $menuProduct = [];
                                    foreach ($subcategory->products as $subProduct) {
                                        if ($subProduct->is_active ==1) {
                                            if (in_array($subProduct->id, $specialProducts)) {
                                                if (!in_array($subProduct->menu->id, $inactive)) {
                                                    if (in_array($subProduct->id, $franchiseProductPrice)) {
                                                        $productsPrices = $franchiseProductId[$subProduct->id];
                                                    } else {
                                                        $productsPrices = $subProduct->price;
                                                    }
                                                    if (!empty($subProduct->tax_id)) {
                                                        $taxRates = $subProduct->tax ? $subProduct->tax->tax_rate : null;
                                                        $taxNames = $subProduct->tax ? $subProduct->tax->tax_name : null;
                                                        $taxTypes = $subProduct->tax ? $subProduct->tax->tax_type : null;
                                                    } else {
                                                        $taxRates = $subcategory->tax ? $subcategory->tax->tax_rate : null;
                                                        $taxNames = $subcategory->tax ? $subcategory->tax->tax_name : null;
                                                        $taxTypes = $subcategory->tax ? $subcategory->tax->tax_type : null;
                                                    }
                                                    $subProductTags = [];
                                                    if (!empty($subProduct->productTag)) {
                                                        foreach ($subProduct->productTag as $subProductTag) {
                                                            $subProductTags[] = [
                                                                'product_tag' => URL('img').'/'.$subProductTag->tag->tag_icon_image,
                                                            ];
                                                        }
                                                    }
                                                    $popularProducts[] = [
                                                        'id' => $subProduct->id,
                                                        'name' => $subProduct->name,
                                                        'price' => $productsPrices,
                                                        'product_code' => $subProduct->product_code,
                                                        'base_price' => $productsPrices,
                                                        'description'   => $subProduct->description,
                                                        'tax_rate'  => $taxRates,
                                                        'tax_name'  => $taxNames,
                                                        'tags'  => $subProductTags,
                                                        'tax_type'  => $taxTypes
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $error) {
            return response()->error(['message' => 'No product Found']);
        }
        return response()->success(['popularItems' => $popularProducts]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkPassword($password)
    {
        $user = User::findOrFail(Auth::User()->id);
        if (Hash::check($password, $user->password)) {
            return true;
        }
    }

    /**
     * For status change of order
     * @param $orderId
     * @return mixed
     */
    public function statusChanged($orderId)
    {
        $status = Order::findOrFail($orderId)->status;
        try {
            \DB::beginTransaction();
            if ($status == 'ordered') {
                Order::where('id', $orderId)->update([
                    'status' => 'in_progress',
                    'progress' => date("Y:m:d H:i:s"),
                    'in_progress_by' => Auth::user()->id,
                ]);
                \DB::commit();
                return response()->success(['message' =>'Order is in progress']);
            }
            if ($status == 'in_progress') {
                Order::where('id', $orderId)->update([
                    'status' => 'ready',
                    'ready_at' => date("Y:m:d H:i:s"),
                    'ready_by' => Auth::user()->id,
                ]);
                $this->sendNotification($orderId);//send nottifcation to the androif user
                \DB::commit();
                return response()->success(['message' =>'Order is ready to serve']);
            }
             if ($status == 'ready') {
                Order::where('id', $orderId)->update([
                    'status' => 'delivered',
                    'delivered_at' => date("Y:m:d H:i:s"),
                    'delivered_by' => Auth::user()->id,
                ]);
                \DB::commit();
                return response()->success(['message' =>'Order is delivered']);
            }
        } catch (\Exception $error) {
            \DB::rollback();
            return response()->error();
        }
    }
    /**
     * For adding payment method
     * @param Request $request
     * @param $orderId
     * @return mixed
     */
    public function payment(Request $request, $orderId)
    {
        $input = $request->all();
        try {
            $payment = Order::findOrFail($orderId)->payment_method;
            if (empty($payment)) {
                \DB::beginTransaction();
                Order::where('id', $orderId)->update([
                    'payment_method' => $input['payment']
                ]);
                \DB::commit();
                return response()->success(['message' => 'Payment method selected']);
            }
        } catch (\Exception $error) {
            \DB::rollback();
            return response()->success(['message' => 'Internal Server Error']);
        }
    }

    /**
     * List all the canceled orders
     * @return mixed
     */
    public function cancelOrderList()
    {
        try {
            $orderCancel = Order::where('status', 'cancelled')->get()->toArray();

            $list='';
            foreach ($orderCancel as $order) {
                $cancelBy = User::whereId($order['cancelled_by'])->first()->name;
                $list[] = [
                    'order_number' => $order['order_number'],
                    'status'       => $order['status'],
                    'cancelled_by'  => $cancelBy,
                    'cancel_reason'=> $order['cancel_reason']
                ];
            }
            return Response::json(array(
                'success' => true,
                'status_code' => 201,
                'order' => $list,
            ));
        } catch (\Exception $error) {
            return Response::json(array(
                'success' => true,
                'status_code' => 500,
                'message' => $error->getMessage()
            ));
        }
    }

    /**
     * List all the edited orders
     * @return mixed
     */
    public function editOrderList()
    {
        try {
            $editOrder = OrderDetail::whereNotNull('removed_by')->whereNotNull('remove_reason')->orderby('order_id')
                ->get()->toArray();
            $editList='';
            foreach ($editOrder as $edit) {
                $cancelBy = User::whereId($edit['removed_by'])->first()->name;
                $order_number = Order::whereId($edit['order_id'])->first()->order_number;
                $product = Product::whereId($edit['product_id'])->first()->name;
                $editList[] = [
                    'product_id' => $product,
                    'order_id'  => $order_number,
                    'quantity'  => $edit['quantity'],
                    'remove_reason' => $edit['remove_reason'],
                    'removed_by' => $cancelBy
                ];
            }
            return Response::json(array(
                'success' => true,
                'status_code' => 201,
                'order' => $editList,
            ));
        } catch (\Exception $error) {
            return Response::json(array(
                'success' => true,
                'status_code' => 500,
                'message' => $error->getMessage()
            ));
        }
    }

    public function getAllProducts()
    {
        try {
            $categories = Category::whereParentId(0)->get();
            $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
            $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
            $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
            $franchiseProductPrice = $franchiseProductId = $menu = $menuProducts = [];
            if (!empty($productPrice)) {
                foreach ($productPrice as $price) {
                    $franchiseProductId[$price['product_id']] = $price['price'];
                    $franchiseProductPrice[] = $price['product_id'];
                }
            }
            foreach ($categories as $category) {
                if ($category->is_active == 1) {
                    if ((count($category->child)>0) || (count($category->products)>0)) {
                        $categoryName = $category->name;
                        foreach ($category->products as $product) {
                            //get product image
                            if ($product->is_active ==1) {
                                if (!in_array($product->menu->id, $inactive)) {
                                    $productName = $product->name;
                                    if (in_array($product->id, $franchiseProductPrice)) {
                                        $productsPrice = $franchiseProductId[$product->id];
                                    } else {
                                        $productsPrice = $product->price;
                                    }
                                    $productImage = $this->getPrductImage($product->id);
                                    if (!empty($product->tax_id)) {
                                        $taxRate = $product->tax ? $product->tax->tax_rate : null;
                                        $taxName = $product->tax ? $product->tax->tax_name : null;
                                        $taxType = $product->tax ? $product->tax->tax_type : null;
                                    } else {
                                        $taxRate = $category->tax ? $category->tax->tax_rate : null;
                                        $taxName = $category->tax ? $category->tax->tax_name : null;
                                        $taxType = $category->tax ? $category->tax->tax_type : null;
                                    }
                                    $productTags = [];
                                    if (!empty($product->productTag)) {
                                        foreach ($product->productTag as $productTag) {
                                            $productTags[] = [
                                                'product_tag' => URL('img').'/'.$productTag->tag->tag_icon_image,
                                            ];
                                        }
                                    }
                                    $menuProducts[] = [
                                        'id' => $product->id,
                                        'name' => $productName,
                                        'price' => $productsPrice,
                                        'product_code' => $product->product_code,
                                        'base_price' => $productsPrice,
                                        'product_image' => $productImage,
                                        'description'   => $product->description,
                                        'tax_rate'  => $taxRate,
                                        'tax_name'  => $taxName,
                                        'tags' => $productTags,
                                        'tax_type'  => $taxType
                                    ];
                                }
                            }
                        }
                        foreach ($category->child as $subcategory) {
                            if ($subcategory->is_active == 1) {
                                if (count($subcategory->products)>0) {
                                    //get product image
                                    $menuProduct = [];
                                    foreach ($subcategory->products as $subProduct) {
                                        if ($subProduct->is_active ==1) {
                                            if (!in_array($subProduct->menu->id, $inactive)) {
                                                if (in_array($subProduct->id, $franchiseProductPrice)) {
                                                    $productsPrices = $franchiseProductId[$subProduct->id];
                                                } else {
                                                    $productsPrices = $subProduct->price;
                                                }
                                                $productImage = $this->getPrductImage($subProduct->id);
                                                if (!empty($subProduct->tax_id)) {
                                                    $taxRates = $subProduct->tax ? $subProduct->tax->tax_rate : null;
                                                    $taxNames = $subProduct->tax ? $subProduct->tax->tax_name : null;
                                                    $taxTypes = $subProduct->tax ? $subProduct->tax->tax_type : null;
                                                } else {
                                                    $taxRates = $subcategory->tax ? $subcategory->tax->tax_rate : null;
                                                    $taxNames = $subcategory->tax ? $subcategory->tax->tax_name : null;
                                                    $taxTypes = $subcategory->tax ? $subcategory->tax->tax_type : null;
                                                }
                                                $subProductTags = [];
                                                if (!empty($subProduct->productTag)) {
                                                    foreach ($subProduct->productTag as $subProductTag) {
                                                        $subProductTags[] = [
                                                            'product_tag' => URL('img').'/'.$subProductTag->tag->tag_icon_image,
                                                        ];
                                                    }
                                                }
                                                $menuProducts[] = [
                                                    'id' => $subProduct->id,
                                                    'name' => $subProduct->name,
                                                    'price' => $productsPrices,
                                                    'product_code' => $subProduct->product_code,
                                                    'base_price' => $productsPrices,
                                                    'product_image' => $productImage,
                                                    'description'   => $subProduct->description,
                                                    'tax_rate'  => $taxRates,
                                                    'tax_name'  => $taxNames,
                                                    'tags'  => $subProductTags,
                                                    'tax_type'  => $taxTypes
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $error) {
            return response()->error(['message' => 'No product Found']);
        }

        return response()->success(['products' => $menuProducts]);
    }

    /**
     * @return mixed
     */
    /**
    * @author Parth Patel <Parth.d9ithub@gmail.com>
    * add product image and product description
    */
    public function popularProductsByCategory()
    {
        $categories = Category::whereParentId(0)->get();
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $productPrice = ProductPrice::where('franchise_id', $franchiseId)->get()->toArray();
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        $franchiseProductPrice = $franchiseProductId = $menu = [];
        if (!empty($productPrice)) {
            foreach ($productPrice as $price) {
                $franchiseProductId[$price['product_id']] = $price['price'];
                $franchiseProductPrice[] = $price['product_id'];
            }
        }
        foreach ($categories as $category) {
            $menuProducts = $menuChild = [];
            if ($category->is_active == 1) {
                if ((count($category->child)>0) || (count($category->products)>0)) {
                    $categoryName = $category->name;
                    $categoryId = $category->id;
                    foreach ($category->products as $product) {
                        //get product image
                        if ($product->is_active ==1) {
                            if (!in_array($product->menu->id, $inactive)) {
                                $productName = $product->name;
                                if (in_array($product->id, $franchiseProductPrice)) {
                                    $productsPrice = $franchiseProductId[$product->id];
                                } else {
                                    $productsPrice = $product->price;
                                }
                                $productImage = $this->getPrductImage($product->id);
                                if (!empty($product->tax_id)) {
                                    $taxRate = $product->tax ? $product->tax->tax_rate : null;
                                    $taxName = $product->tax ? $product->tax->tax_name : null;
                                    $taxType  = $product->tax ? $product->tax->tax_type : null;
                                } else {
                                    $taxRate = $category->tax ? $category->tax->tax_rate : null;
                                    $taxName = $category->tax ? $category->tax->tax_name : null;
                                    $taxType  = $product->tax ? $product->tax->tax_type : null;

                                }
                                $productTags = [];
                                if (!empty($product->productTag)) {
                                    foreach ($product->productTag as $productTag) {
                                        $productTags[] = [
                                            'product_tag' => URL('img').'/'.$productTag->tag->tag_icon_image,
                                        ];
                                    }
                                }
                                $menuProducts[] = [
                                    'id'            => $product->id,
                                    'name'          => $productName,
                                    'price'         => $productsPrice,
                                    'product_code'  => $product->product_code,
                                    'base_price'    => $productsPrice,
                                    'product_image' => $productImage,
                                    'description'   => $product->description,
                                    'tax_rate'      => $taxRate,
                                    'tax_name'      => $taxName,
                                    'tags'          => $productTags,
                                    'tax_type'      => $taxType
                                ];
                            }
                        }
                    }
                    foreach ($category->child as $subcategory) {
                        if ($subcategory->is_active == 1) {
                            if (count($subcategory->products)>0) {
                                //get product image
                                $menuProduct = [];
                                foreach ($subcategory->products as $subProduct) {
                                    if ($subProduct->is_active ==1) {
                                        if (!in_array($subProduct->menu->id, $inactive)) {
                                            if (in_array($subProduct->id, $franchiseProductPrice)) {
                                                $productsPrices = $franchiseProductId[$subProduct->id];
                                            } else {
                                                $productsPrices = $subProduct->price;
                                            }
                                            $productImage = $this->getPrductImage($subProduct->id);
                                            if (!empty($subProduct->tax_id)) {
                                                $taxRates = $subProduct->tax ? $subProduct->tax->tax_rate : null;
                                                $taxNames = $subProduct->tax ? $subProduct->tax->tax_name : null;
                                                $taxTypes  = $subProduct->tax ? $subProduct->tax->tax_type : null;
                                            } else {
                                                $taxRates = $subcategory->tax ? $subcategory->tax->tax_rate : null;
                                                $taxNames = $subcategory->tax ? $subcategory->tax->tax_name : null;
                                                $taxTypes  = $subcategory->tax ? $subcategory->tax->tax_type : null;
                                            }
                                            $subProductTags = [];
                                            if (!empty($subProduct->productTag)) {
                                                foreach ($subProduct->productTag as $subProductTag) {
                                                    $subProductTags[] = [
                                                        'product_tag' => URL('img').'/'.$subProductTag->tag->tag_icon_image,
                                                    ];
                                                }
                                            }
                                            $menuProduct[] = [
                                                'id'            => $subProduct->id,
                                                'name'          => $subProduct->name,
                                                'price'         => $productsPrices,
                                                'product_code'  => $subProduct->product_code,
                                                'base_price'    => $productsPrices,
                                                'product_image' => $productImage,
                                                'description'   => $subProduct->description,
                                                'tax_rate'      => $taxRates,
                                                'tax_name'      => $taxNames,
                                                'tags'          => $subProductTags,
                                                'tax_type'      => $taxTypes
                                            ];
                                        }
                                    }
                                }
                                $menuChild[] = [
                                    'sub_category_name' => $subcategory->name,
                                    'sub_category_id'   => $subcategory->id,
                                    'tax_rate'  => $subcategory->tax ? $subcategory->tax->tax_rate : null,
                                    'products' => $menuProduct,
                                ];
                            }
                        }
                    }
                    $menu[] = [
                        'category_name' => $categoryName,
                        'category_id'   => $categoryId,
                        "tax_rate"      => $category->tax ? $category->tax->tax_rate : null,
                        'products' => $menuProducts,
                        'child' => $menuChild
                    ];
                }
            }
        }
        return response()->success(['menuItems' => $menu]);
    }

    /**
     * @param productId
     * @return product_image
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function getPrductImage($productId)
    {
        $productData = DB::table('product_photos')->where('product_id',$productId)->first();
        if($productData != null){
            $productImage = \URL::to('/').'/upload/'.$productData->file_name;
        }else{
            $productImage = "";
        }
        return $productImage;
    }

    public function getAllTaxes(Request $request)
    {
        $taxesAndDiscount = getTaxes($request->get('products'), $request->get('total'));
        return response()->success(['taxAndDiscount' => $taxesAndDiscount]);
    }

    public function getAllTax()
    {
        /*$franchiseId= \App\Employee::whereUserId(Auth::user()->id)->first()->franchise_id;*/
        $taxes = \App\Tax::pluck('tax_rate', \DB::raw('REPLACE(tax_name, " ", "_") as name'));
        $serviceTax =  \App\Tax::whereTaxType(2)->first();
        $taxNameWithType = \App\Tax::pluck('tax_type', \DB::raw('REPLACE(tax_name, " ", "_") as name'));
        $allTaxTypes = \Config::get('constants.TAX_TYPE');
        return response()->success(['allTaxes' => $taxes, 'service_tax' => $serviceTax,
            'tax_type' => $taxNameWithType, 'all_tax_type' =>$allTaxTypes]);
    }

    /**
     * @param OrderRequest $request
     * @return mixed
     */
    public function storeMultipleOrder(OrderRequest $request)
    {
        $input = $request->all();
        $order = [];
        $discount='';
        if (!empty($input['orders']))  {
            try {
                \DB::beginTransaction();
                foreach ($input['orders'] as $orders) {
                    $franchiseId = Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
                    $taxes = Tax::whereFranchiseId($franchiseId)->pluck('tax_rate', 'tax_name')->toArray();
                    if ($orders['customer_id'] == 'newUser') {
                        $customer = Customer::create([
                            'name' => $orders['customer']['display'],
                            'email' => $orders['customer']['email'],
                            'contact_number' => $orders['customer']['contact_number']
                        ]);
                        $order = Order::create([
                            'customer_id'   => $customer->id,
                            'order_number'  => (new Order())->getNewOrderNumber(),
                            'payment_method' => isset($orders['payment'])?$orders['payment']:null,
                            'order_taken_by' => Auth::user()->id,
                            'transaction_id' => $orders['transaction_id'],
                            'created_at'    => date('Y-m-d H:i:s', $orders['created_at']),
                            'cash_given' => isset($orders['cash_given'])?$orders['cash_given']:0,
                            'sub_total' => $orders['sub_total'],
                            'discount' => $orders['discounts'],
                            'offer' => $orders['offer'],
                            'tax_collected' => $orders['tax_collected'],
                            'grand_total' => $orders['grand_total'],
                        ]);
                    } else {
                        $order = Order::create([
                            'customer_id'   => $orders['customer_id'],
                            'order_number'  => (new Order())->getNewOrderNumber(),
                            'payment_method' => isset($orders['payment'])?$orders['payment']:null,
                            'order_taken_by' => Auth::user()->id,
                            'transaction_id' => $orders['transaction_id'],
                            'created_at'    => date('Y-m-d H:i:s', $orders['created_at']),
                            'cash_given' => isset($orders['cash_given'])?$orders['cash_given']:0,
                            'sub_total' => $orders['sub_total'],
                            'discount' => $orders['discounts'],
                            'offer' => $orders['offer'],
                            'tax_collected' => $orders['tax_collected'],
                            'grand_total' => $orders['grand_total'],
                        ]);
                    }

                    $productId = $quantity = $orderDetails = [];
                    foreach ($orders['order'] as $orderDetail) {
                        $productId[] = $orderDetail['product_id'];
                        $quantity[] = $orderDetail['quantity'];
                        $created_at = date('Y-m-d', strtotime($order['created_at']));
                        $discountAmount = 0;
                        /*if (isset($input['discount_type'])){
                            if($input['discount_type'] == 'fixed'){
                                $discountAmount = $input['discount_amount'];
                            } else {
                                $discountAmount = (($orderDetail['price']*$input['discount_amount']) / 100);
                            }
                        } else {
                            $discountAmount = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                        }*/
                        $offers = DiscountOfferRule::whereRuleType('offer')->where('is_active', '1')
                            ->where(function ($query) use($created_at) {
                                $query->where('from_date', '<=', $created_at)->where('to_date', '>=', $created_at);
                            })->get()->toArray();
                        $offerAmount = '';
                        $categories =  [];
                        if (!empty($offers)) {
                            if (is_array($productId)) {
                                foreach ($productId as $product) {
                                    $menuProducts = Menu::pluck('product_id')->toArray();
                                    if (in_array($product, $menuProducts)) {
                                        $categories[] = Menu::where('product_id', $product)->first()->category_id;
                                    }

                                }
                            }
                            $offerDiscount =[];

                            foreach ($offers as $offer) {
                                $amount = $offer['amount'];
                                $discountQty = $offer['discount_qty_step'];
                                $orderProducts = $discountQty+round($offer['amount']);
                                $amountType = $offer['amount_type'];
                                $condition = json_decode($offer['conditions'], true);
                                $type = $condition['type'];
                                if ($type == 'products') {
                                    $id = $condition['ids'];
                                    $productExists = array_intersect($productId, $id);
                                    if (empty($productExists)) {
                                        $offerDiscount[] = '';
                                    } else {
                                        if ($amountType == 'fixed') {
                                            $offerDiscount[] = $amount;
                                        } elseif ($amountType == 'percent') {
                                            $offerDiscount[] = ($orderDetail['base_price'] * $amount) / 100;
                                        } else {
                                            for ($q = 1; $q < 2000; $q++) {
                                                if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                                    $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                                }
                                            }
                                        }
                                    }
                                } elseif ($type == 'all') {
                                    if ($amountType == 'fixed') {
                                        $offerDiscount[] = $amount;
                                    } elseif ($amountType == 'percent') {
                                        $offerDiscount[] = ($orderDetail['price'] * $amount) / 100;
                                    } else {
                                        for ($q = 1; $q < 2000; $q++) {
                                            if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                                $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                            }
                                        }
                                    }
                                } else {
                                    $id = $condition['ids'];
                                    $categoryExists = array_intersect($categories, $id);

                                    if (empty($categoryExists)) {
                                        $offerDiscount[] = '';
                                    } else {
                                        if ($amountType == 'fixed') {
                                            $offerDiscount[] = $amount;
                                        } elseif ($amountType == 'percent') {
                                            $offerDiscount[] = ($orderDetail['base_price'] * $amount) / 100;
                                        } else {
                                            for ($q = 1; $q < 2000; $q++) {
                                                if ($orderDetail['quantity'] >= ($orderProducts*$q)) {
                                                    $offerDiscount[] = ($orderDetail['base_price'] * $amount);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $offerAmount = array_sum($offerDiscount);
                        }

                        //$offerAmount = offerAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                        if($offerAmount){
                            $offerAmount = $offerAmount;
                            $discountAmount = 0;
                        } else {
                            $offerAmount = 0;
                            if (isset($orders['discount_type'])){
                                if($orders['discount_type'] == 'fixed'){
                                    $discountAmount = $orders['discount_amount'];
                                } else {
                                    $discountAmount = (($orderDetail['price']*$orders['discount_amount']) / 100);
                                }
                            } else {
                                $discountAmount = discountAmount($orderDetail['product_id'], $orderDetail['price'], $created_at);
                            }
                        }
                        //$discount = $discounts + $discountAmount;
                        $totalPrices = $orderDetail['price'] - $discountAmount -$offerAmount;
                        /*$serviceCharge = $vat = $serviceTax = $serviceChargeAmount =  $vatAmount = $serviceTaxAmount = 0;*/
                        $sgstTax = $cgstTax = 0;
                        /*if (isset($taxes['Service Charge'])) {
                            $serviceCharge = round(($totalPrices * $taxes['Service Charge']) / 100, 2);
                        }
                        $subTotal = $totalPrices + $serviceCharge;
                        if (isset($taxes['VAT'])) {
                            $vat = round(($subTotal * $taxes['VAT']) / 100, 2);
                        }
                        if (isset($taxes['Service Tax'])) {
                            $serviceTax = round(($subTotal * $taxes['Service Tax']) / 100, 2);
                        }
                        if (isset($taxes['GST'])) {
                            $gstTax = round(($subTotal * $taxes['GST']) / 100, 2);
                        }*/
                        if (isset($taxes['SGST'])) {
                            $gstTax = round(($totalPrices * $taxes['SGST']) / 100, 2);
                        }
                        if (isset($taxes['CGST'])) {
                            $gstTax = round(($totalPrices * $taxes['CGST']) / 100, 2);
                        }
                        $billAmount = round($totalPrices + $sgstTax + $cgstTax);
                        $orderDetails[] = OrderDetail::create([
                            'product_id' => $orderDetail['product_id'],
                            'order_id'   => $order->id,
                            'quantity'   => $orderDetail['quantity'],
                            'sub_total'  => $orderDetail['price'],
                            'discount' => $discountAmount,
                            'offer' => $offerAmount,
                            'tax_collected' => ($sgstTax + $cgstTax),
                            'grand_total' => $billAmount
                        ]);
                    }
                    $order['order'] = $orderDetails;
                }
                Log::useDailyFiles(public_path().'/logs/orders.log');
                Log::info('Order Place Successfully: '.$order);
                \DB::commit();
            } catch (\Exception $error) {
                \DB::rollBack();
                Log::useDailyFiles(public_path().'/logs/errors.log');
                Log::error('Error: '.$error->getMessage());
                return response()->error('Internal server error. Please contact administrator');
            }
        }
        return response()->success(['message' => 'Order Place Successfully', 'orders' => $order]);
    }

    /**
     * @return mixed
     */
    public function getCurrentTime()
    {
        $date = Time();
        return response()->success(['dateTime' => $date]);
    }

    /**
     * @return mixed
     */
    public function getOffers(){
        $offers = DiscountOfferRule::whereRuleType('offer')->where('is_active', '1')->whereAmountType('buy_x_get_y_free')->get();
        return response()->success(['offers' => $offers]);
    }

    /**
     * @return mixed
     */
    public function getDiscount(){
        $discount = DiscountOfferRule::whereRuleType('discount')->where('is_active', '1')->get();
        return response()->success(['discounts' => $discount]);
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        $categories = Category::whereParentId(0)->get();
        $franchiseId = Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
        $inactive = InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
        foreach ($categories as $category) {
            $menuProducts = $menuChild = [];
            if ($category->is_active == 1) {
                if ((count($category->child)>0) || (count($category->products)>0)) {
                    $categoryName = $category->name;
                    foreach ($category->products as $product) {
                        if ($product->is_active ==1) {
                            if (!in_array($product->menu->id, $inactive)) {
                                $menuProducts[] = [
                                    'product_id' => $product->id,
                                ];
                            }
                        }
                    }
                    foreach ($category->child as $subcategory) {
                        if ($subcategory->is_active == 1) {
                            if (count($subcategory->products)>0) {
                                $menuProduct = [];
                                foreach ($subcategory->products as $subProduct) {
                                    if ($subProduct->is_active ==1) {
                                        if (!in_array($subProduct->menu->id, $inactive)) {
                                            $menuProduct[] = [
                                                'product_id' => $subProduct->id,
                                            ];
                                        }
                                    }
                                }
                                $menuChild[] = [
                                    'sub_category_name' => $subcategory->name,
                                    'category_id' => $subcategory->id,
                                    'products' => $menuProduct,
                                ];
                            }
                        }
                    }
                    $menu[] = [
                        'category_id' => $category->id,
                        'category_name' => $categoryName,
                        'products' => $menuProducts,
                        'child' => $menuChild
                    ];
                }
            }
        }
        return response()->success(['menus' => $menu]);
    }
    /**
     * send notification to the user
     *
     * @param  customerId , order
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function sendNotification($orderId)
    {
        $order = Order::find($orderId);
        if(count($order) > 0){
            $user = User::find($order->order_taken_by);
            $order_number = $order->order_number;
            if(count($user) > 0){            
                if($user->mobile_id != null){
                     $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                     $token=$user->mobile_id;
                    $notification = [
                            'title' =>'TCL',
                            'body' => "Order $order_number is ready to serve.",
                            'icon' =>'myicon', 
                            'sound' => 'default',
                            'click_action'=>'OPEN_ACTIVITY_1'
                        ];
                        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

                        $fcmNotification = [
                            //'registration_ids' => $tokenList, //multple token array
                            'data' => [ 'title' =>'TCL',
                                        'body' => "Order $order_number is ready to serve."],
                            'notification' => $notification,
                            'to'        => $token, //single token
                        ];
                        $headers = [
                            'Authorization: key=' . "AAAAy9WkUvU:APA91bGvtfhCF_nXoXdmkdQAzu5yB_Qye-hhmVxNG_qzq2RtqThSMgHXFocCx8ISasS4ckmZIyPKF4GMJMOA8jE-EdcfrrQNvSvnVjtbAHjtGvKcdZB9gHrXDn0KFtubUjtM5UH_affa",
                            'Content-Type: application/json'
                        ];


                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                        $result = curl_exec($ch);
                        curl_close($ch);        
                }
            }
        }
    }
    /**
     * get all tables
     *
     * @param  
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */
    public function getTables()
    {
        try{
            $ManageTable = ManageTable::where('is_active',1)->pluck('name','id')->toArray();
        }catch(\Exception $error){
            return response()->error(['tables' => '']);
        }
        return response()->success(['tables' => $ManageTable]);
    }
}
