<?php
/**
 * @package App/Http/Controllers
 *
 * @class ReportController
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\DiscountOfferRule;
use App\Employee;
use App\Franchise;
use App\Menu;
use App\NcOrder;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\ProductPrice;
use App\Tax;
use App\User;
use App\Tag;
use App\DeviceType;
use App\CustomerDevices;
use App\CustomerWalletHistory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    /**
     * To track delivery time of order
     *
     * @return mixed
     */
    public function trackTime()
    {
        if (superAdmin()) {
            $franchiseId = Input::get('franchise');
        } else {
            $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        }
        $fromDate =  Input::get('form_date');
        $toDate =  Input::get('to_date');
        $franchise = Franchise::pluck('name', 'id')->toArray();
        $users = User::withTrashed()->pluck('name', 'id')->toArray();
        $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
        $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
        $orderLists = Order::whereStatus('delivered');
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        if(!empty($franchiseId)) {
            $orderLists = $orderLists->whereIn('order_taken_by', $employees);
        }
        if (!empty($fromDate)) {
            $orderLists = $orderLists->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $orderLists = $orderLists->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $orderLists = $orderLists->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'=', date('Y-m-d H:i'));
        }
        $orderLists = $orderLists->get()->toArray();
        $orders = [];
        foreach ($orderLists as $list) {
            $createdtime = new Carbon($list['created_at']);
            $deliveredTime = new Carbon($list['delivered_at']);
            $hours = $deliveredTime->diff($createdtime)->format("%h:%i:%s");
            $orders[] = [
                'order_number' => $list['order_number'],
                'created_at' => $list['created_at'],
                'delivered_at' => $list['delivered_at'],
                'order_taken' =>  $users[$list['order_taken_by']],
                'time' => $hours
            ];
        }
        $orders = customPaginate($orders);
        return view('report.timeTrack',
            compact('orders', 'franchise', 'franchiseId', 'fromDate', 'toDate')
        );
    }

    /**
     * Order time track Excel report
     */
    public function trackOrderTimeExcel()
    {
        $filename = 'Order'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {

            $excel->sheet($filename, function ($sheet) {
                if (superAdmin()) {
                    $franchiseId = Input::get('franchise');
                } else {
                    $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                }
                $fromDate =  Input::get('form_date');
                $toDate =  Input::get('to_date');
                $franchise = Franchise::pluck('name', 'id')->toArray();
                $users = User::withTrashed()->pluck('name', 'id')->toArray();
                $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
                $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
                $orderLists = Order::whereStatus('delivered');
                $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
                if(!empty($franchiseId)) {
                    $orderLists = $orderLists->whereIn('order_taken_by', $employees);
                }
                if (!empty($fromDate)) {
                    $orderLists = $orderLists->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
                }
                if (!empty($toDate)) {
                    $orderLists = $orderLists->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
                }
                if (true === $noDateFilter) {
                    $orderLists = $orderLists->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
                }
                $orderLists = $orderLists->get()->toArray();
                $orders = '';
                foreach ($orderLists as $list) {
                    $createdtime = new Carbon($list['created_at']);
                    $deliveredTime = new Carbon($list['delivered_at']);
                    $hours = $deliveredTime->diff($createdtime)->format("%h:%i:%s");
                    $orders[] = [
                        'order_number' => $list['order_number'],
                        'created_at' => $list['created_at'],
                        'delivered_at' => $list['delivered_at'],
                        'order_taken' =>  $users[$list['order_taken_by']],
                        'time' => $hours
                    ];
                }
                $sheet->loadView(
                    'report.track-time-excel',
                    ['orders' => $orders, 'franchise' => $franchise,
                        'franchiseId' => $franchiseId, 'fromDate'=> $fromDate, 'toDate' => $toDate]
                );
            });
        })->export('xls');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View'
     */
    public function categoryWise()
    {
        if (superAdmin()) {
            $franchiseId = Input::get('franchise');
        } else {
            $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        }
        $franchise = Franchise::pluck('name', 'id')->toArray();
        $fromDate =  Input::get('form_date');
        $toDate =  Input::get('to_date');
        $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
        $viewOrder = $this->categoryItemCount($whereFranchiseId, $toDate, $fromDate);
        $viewOrder = customPaginate($viewOrder);
        return view('report.categorySale', compact('viewOrder', 'franchise', 'franchiseId', 'fromDate', 'toDate'));
    }

    /**
     * category wise excel
     */
    public function categoryWiseExcel()
    {
        $filename = 'categoryWise'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {

            $excel->sheet($filename, function ($sheet) {
                if (superAdmin()) {
                    $franchiseId = Input::get('franchise');
                } else {
                    $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                }
                $franchise = Franchise::pluck('name', 'id')->toArray();
                $fromDate =  Input::get('form_date');
                $toDate =  Input::get('to_date');
                $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
                $viewOrder = $this->categoryItemCount($whereFranchiseId, $toDate, $fromDate);
                $sheet->loadView(
                    'report.category-wise-excel',
                    ['orderDetail' => $viewOrder, 'franchise' => $franchise,
                        'franchiseId' => $franchiseId, 'fromDate'=> $fromDate, 'toDate' => $toDate]
                );
            });
        })->export('xls');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
       /**
 * @package App/Http/Controllers
 *
 * @class ReportController
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
    public function itemSale()
    {
        if (superAdmin()) {
            $franchiseId = Input::get('franchise');
        } else {
            $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        }
        $tags = Tag::get()->pluck('name','id')->toArray();
        $franchise = Franchise::pluck('name', 'id')->toArray();
        $fromDate =  Input::get('form_date');
        $toDate =  Input::get('to_date');
        $tagsId = Input::get('tags');
        $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
        $viewOrder = $this->itemSaleReport($whereFranchiseId, $fromDate, $toDate);
        //start--developed by Parth Patel date:-08-11-2017
        foreach ($viewOrder as $key => $view) {            
            if (!empty($tagsId)) {
                        $check_tag = DB::table('product_tags')
                                    ->where('product_id',$view['product_id'])
                                    ->where('tag_id',$tagsId)
                                    ->exists();
                        if (!$check_tag) {
                            unset($viewOrder[$key]);
                        }
                    }
        }
        //--end
        $viewOrder = customPaginate($viewOrder);
        return view('report.itemSale', compact('viewOrder', 'franchise', 'franchiseId', 'fromDate', 'toDate','tags'));
    }

    /**
     * Item sale Report excel
     */
    public function itemSaleExcel()
    {
        $filename = 'ItemSale'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {
            $excel->sheet($filename, function ($sheet) {
                if (superAdmin()) {
                    $franchiseId = Input::get('franchise');
                } else {
                    $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                }
                $franchise = Franchise::pluck('name', 'id')->toArray();
                $fromDate =  Input::get('form_date');
                $toDate =  Input::get('to_date');
                $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
                $viewOrder = $this->itemSaleReport($whereFranchiseId, $fromDate, $toDate);
                $sheet->loadView(
                    'report.item-sale-excel',
                    ['orderDetail' => $viewOrder, 'franchise' => $franchise,
                        'franchiseId' => $franchiseId, 'fromDate'=> $fromDate, 'toDate' => $toDate]
                );
            });
        })->export('xls');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /**
 * @package App/Http/Controllers
 *
 * @class ReportController
 *
 * @author Parth Patel <parth.d9ithub@gmail.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
    public function saleReport()
    {
        if (superAdmin()) {
            $franchiseId = Input::get('franchise');
        } else {
            $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        }
        $payment_method_Id = Input::get('payment_method');
        $franchise = Franchise::pluck('name', 'id')->toArray();
        $payment_method = \Config::get('constants.Payment_Method');
        $fromDate =  Input::get('form_date');
        $toDate =  Input::get('to_date');
        $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
        $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
        $users = User::withTrashed()->pluck('name', 'id')->toArray();
        $ncOrders = NcOrder::pluck('order_id')->toArray();
        $orders = Order::with('ncOrder.nonChargeablePeople')->whereCancelledBy(null);
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
            //start-- develop by Parth Patel date:-08-11-017 add payment method filter for get data
        //--end
        if(!empty($franchiseId)) {
            $orders = $orders->whereIn('order_taken_by', $employees);
        }
        if (!empty($fromDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=',userTimeToUTC($toDate));
        }
        if(!empty($payment_method_Id)){
            if ($payment_method_Id == 5) {
                $orders = $orders->whereIn('id', $ncOrders);
            } else {
                $orders = $orders->whereNotIn('id', $ncOrders)->where('payment_method', $payment_method_Id);
            }
        }
        if (true === $noDateFilter) {
            $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }
        $orders = $orders->get()->toArray();
        
        $total = [];
        $i=0;

        foreach ($orders as $totalOrder) {
            //start-- develop by Parth Patel date:-08-11-017 set payment method to order data
            if (array_key_exists($totalOrder['payment_method'],$payment_method)) {
                $orders[$i]['payment_method'] = $payment_method[$totalOrder['payment_method']];
                $i++;
            }
            foreach ($totalOrder as $id => $value) {
                if (array_key_exists( $id, $total ) && in_array($id, ['sub_total', 'grand_total', 'tax_collected'])) {
                    $total[$id] += $value;
                } else {
                    $total[$id] = $value;
                }
            }
        }
        $total['total_transaction'] = count($orders);
        $ordersPrice = customPaginate($orders);
        return view('report.saleReport', compact('ordersPrice', 'franchise', 'franchiseId', 'fromDate', 'toDate',
        'users', 'total','payment_method', 'payment_method_Id'));
    }

    /**
     * Sales report excel
     */
    public function saleReportExcel()
    {
        $filename = 'SaleReport'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {

            $excel->sheet($filename, function ($sheet) {
                if (superAdmin()) {
                    $franchiseId = Input::get('franchise');
                } else {
                    $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                }
                $payment_method_Id = Input::get('payment_method');
                $franchise = Franchise::pluck('name', 'id')->toArray();
                $fromDate =  Input::get('form_date');
                $toDate =  Input::get('to_date');
                $payment_method = \Config::get('constants.Payment_Method');
                $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
                $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
                $users = User::withTrashed()->pluck('name', 'id')->toArray();
                $ncOrders = NcOrder::pluck('order_id')->toArray();
                $orders = Order::with('ncOrder.nonChargeablePeople')->whereCancelledBy(null);
                $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
                if(!empty($payment_method_Id)){
                    if ($payment_method_Id == 5) {
                        $orders = $orders->whereIn('id', $ncOrders);
                    } else {
                        $orders = $orders->whereNotIn('id', $ncOrders)->where('payment_method', $payment_method_Id);
                    }
                }
                //--end
                if(!empty($franchiseId)) {
                    $orders = $orders->whereIn('order_taken_by', $employees);
                }
                if (!empty($fromDate)) {
                    $orders = $orders->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
                }
                if (!empty($toDate)) {
                    $orders = $orders->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
                }
                if (true === $noDateFilter) {
                    $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
                }
                $orders = $orders->get()->toArray();
                $total = [];
                $i=0;
                foreach ($orders as $totalOrder) {
                    //start-- develop by Parth Patel date:-08-11-017 set payment method to order data
                    if (array_key_exists($totalOrder['payment_method'],$payment_method)) {
                        $orders[$i]['payment_method'] = $payment_method[$totalOrder['payment_method']];
                        $i++;
                    }
                    foreach ($totalOrder as $id => $value) {
                        if (array_key_exists( $id, $total ) ) {
                            $total[$id] += $value;
                        } else {
                            $total[$id] = $value;
                        }
                    }
                }
                $total['total_transaction'] = count($orders);
                $ordersPrice = $orders;
                $sheet->loadView(
                    'report.sale-excel',
                    ['ordersPrice' => $ordersPrice, 'franchise' => $franchise,
                        'franchiseId' => $franchiseId, 'fromDate'=> $fromDate, 'toDate' => $toDate, 'users' => $users, 'payment_method_Id' => $payment_method_Id
                    ]
                );
            });
        })->export('xls');
    }

    /**
     * @param $franchiseId
     * @param $toDate
     * @param $fromDate
     * @return array
     */
    private function categoryItemCount($franchiseId, $toDate, $fromDate)
    {
        $employees = Employee::whereIn('franchise_id', $franchiseId)->pluck('user_id')->toArray();
        $categoryName = Category::pluck('name', 'id')->toArray();
        $orders =  Order::with('orderDetail.product.menu')->whereCancelledBy(null);
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        if(!empty($franchiseId)) {
            $orders = $orders->whereIn('order_taken_by', $employees);
        }
        if (!empty($fromDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }
        $orders = $orders->get()->toArray();
        $productName = Product::pluck('name', 'id')->toArray();
        $viewOrder = $detail = [];
        $franchiseIds = '';
        foreach ($orders as $order) {
            foreach ($order['order_detail'] as $orderDetail) {
                if ($orderDetail['removed_by'] == null) {
                    $detail[]  = [
                            'id' => $orderDetail['id'],
                            'product_id' => $orderDetail['product_id'],
                            'product_name' => $productName[$orderDetail['product_id']],
                            'quantity' => $orderDetail['quantity'],
                            'sub_total' => $orderDetail['sub_total'],
                            'product_code' => $orderDetail['product']['product_code'],
                            'category_name' => isset($categoryName[$orderDetail['product']['menu']['category_id']])?$categoryName[$orderDetail['product']['menu']['category_id']]:'N/A',
                            'category_id'   =>  $orderDetail['product']['menu']['category_id'],
                            'count'         => 1,
                            'franchise_id'  => $franchiseIds,
                            'created_at'    => $order['created_at'],
                            'grand_total'  => $orderDetail['grand_total']
                    ];
                    //$productTotal[] = ($productsPrices * $orderDetail['quantity']);
                }
            }
        }
        $partnerSums=array();
        foreach ($detail as $res) {
            if (!array_key_exists($res['category_id'], $partnerSums)) {
                $partnerSums[$res['category_id']] = $res;
            } else {
                $partnerSums[$res['category_id']]['count'] += $res['count'];
                $partnerSums[$res['category_id']]['sub_total'] += $res['sub_total'];
                $partnerSums[$res['category_id']]['quantity'] += $res['quantity'];
                $partnerSums[$res['category_id']]['grand_total'] += $res['grand_total'];
            }
        }
        $partnerSums = array_values($partnerSums);
        $total = [];
        foreach($partnerSums as $key => $row){
            foreach ($row as $id => $value) {
                if (array_key_exists( $id, $total ) ) {
                    $total[$id] += $value;
                } else {
                    $total[$id] = $value;
                }
            }
        }
        foreach($partnerSums as $key => $row){
            if ($total['grand_total']) {
                $percentage = round(($row['grand_total'] / $total['grand_total']) * 100, 2);
                $viewOrder[] = [
                    'id' => $row['id'],
                    'category_name' => $row['category_name'],
                    'quantity' => $row['quantity'],
                    'sub_total' => $row['sub_total'],
                    'grand_total' => $row['grand_total'],
                    'count'         => $row['count'],
                    'percent' => $percentage,
                    'total' => $total,
                    //'category_name' => $productsDetail['category_name']
                ];
            }

        }
        foreach($viewOrder as $key => $row){
            $new_published[$key] = $row['percent'];
        }
        if(!empty($viewOrder)){
            array_multisort($new_published, SORT_DESC, $viewOrder);
        }
        return $viewOrder;
    }

    /**
     * @return mixed
     */
    public function totalSale()
    {
        if (superAdmin()) {
            $franchiseId = Input::get('franchise');
        } else {
            $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        }
        $franchise = Franchise::pluck('name', 'id')->toArray();
        $fromDate =  Input::get('form_date');
        $toDate =  Input::get('to_date');
        $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
        $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
        $orders = Order::whereCancelledBy(null);
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        if(!empty($franchiseId)) {
            $orders = $orders->whereIn('order_taken_by', $employees);
        }
        if (!empty($fromDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }
        $orders = $orders->get()->toArray();
        $totalSale = $createDate = $total = $cash = $card = $other = $wallet = $viewOrder =[];
        foreach ($orders as $key => $totalOrder) {
            $createdAt = date('d-m-y', strtotime($totalOrder['created_at']));
            $grandTotal = $totalOrder['grand_total'];
            $createDate[] = $createdAt;
            if (!array_key_exists($createdAt, $totalSale)) {
                $totalSale[$createdAt] = $grandTotal;
            } else {
                $totalSale[$createdAt] +=  $grandTotal;
            }

            if ($totalOrder['payment_method'] == '1') {
                if (!array_key_exists($key, $totalSale)) {
                    $cash[$key] = $grandTotal;
                } else {
                    $cash[$key] +=  $grandTotal;
                }
            }
            if ($totalOrder['payment_method'] == '2') {
                if (!array_key_exists($key, $totalSale)) {
                    $card[$key] = $grandTotal;
                } else {
                    $card[$key] +=  $grandTotal;
                }
            }
            if($totalOrder['payment_method'] == '3') {
                if (!array_key_exists($key, $totalSale)) {
                    $other[$key] = $grandTotal;
                } else {
                    $other[$key] +=  $grandTotal;
                }
            }
            if($totalOrder['payment_method'] == '4') {
                if (!array_key_exists($key, $totalSale)) {
                    $wallet[$key] = $grandTotal;
                } else {
                    $wallet[$key] +=  $grandTotal;
                }
            }
            $number = $totalOrder['discount'];
            $whole = floor($number);      // 1
            $fraction = $number - $whole;
            if ($fraction <= 0.5) {
                $discount = $whole;
            } else {
                $discount = round($totalOrder['discount']);
            }
            $viewOrder = [
                'sub_total' => $totalOrder['sub_total'],
                'grand_total' => $totalOrder['grand_total'],
                'discount'   =>  $totalOrder['discount'],
                'offer'   =>    $totalOrder['offer'],
                'tax_collected'   =>    $totalOrder['tax_collected'],
            ];
            foreach ($viewOrder as $id => $value) {
                if ( array_key_exists( $id, $total ) ) {
                    $total[$id] += $value;
                } else {
                    $total[$id] = $value;
                }
            }
        }
        $cash = array_sum($cash);
        $card = array_sum($card);
        $other = array_sum($other);
        $wallet = array_sum($wallet);
        $totalSale =  array_values($totalSale);
        $createDate = array_values(array_unique($createDate));
            return view('report.totalSale')
                ->with('franchiseId', $franchiseId)
                ->with('franchise', $franchise)
                ->with('fromDate', $fromDate)
                ->with('toDate', $toDate)
                ->with('total', $total)
                ->with('cash', $cash)
                ->with('card', $card)
                ->with('other', $other)
                ->with('wallet', $wallet)
                ->with('totalSale', json_encode($totalSale, JSON_NUMERIC_CHECK))
                ->with('createDate', json_encode($createDate, JSON_NUMERIC_CHECK));
    }

    /**
     * Total Sales report excel
     */
    public function totalSaleExcel()
    {
        $filename = 'TotalSale'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {
            $excel->sheet($filename, function ($sheet) {
                if (superAdmin()) {
                    $franchiseId = Input::get('franchise');
                } else {
                    $franchiseId = Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
                }
                $franchise = Franchise::pluck('name', 'id')->toArray();
                $fromDate =  Input::get('form_date');
                $toDate =  Input::get('to_date');
                $users = User::withTrashed()->pluck('name', 'id')->toArray();
                $whereFranchiseId = !$franchiseId ? array_keys($franchise) : [$franchiseId];
                $employees = Employee::whereIn('franchise_id', $whereFranchiseId)->pluck('user_id')->toArray();
                $orders = Order::whereCancelledBy(null);
                $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
                if(!empty($franchiseId)) {
                    $orders = $orders->whereIn('order_taken_by', $employees);
                }
                if (!empty($fromDate)) {
                    $orders = $orders->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
                }
                if (!empty($toDate)) {
                    $orders = $orders->whereIn('order_taken_by', $employees)
                        ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
                }
                if (true === $noDateFilter) {
                    $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
                }
                $orders = $orders->get()->toArray();
                $totalSale = $createDate = [];
                foreach ($orders as $totalOrder) {
                    $createdAt = date('d-m-y', strtotime($totalOrder['created_at']));
                    $createDate[] = $createdAt;
                    $number = $totalOrder['discount'];
                    $whole = floor($number);      // 1
                    $fraction = $number - $whole;
                    if ($fraction <= 0.5) {
                        $discount = $whole;
                    } else {
                        $discount = round($totalOrder['discount']);
                    }
                    if (!array_key_exists($createdAt, $totalSale)) {
                        $totalSale[$createdAt] = $totalOrder;
                    } else {
                        $totalSale[$createdAt]['grand_total'] +=  $totalOrder['grand_total'];
                        $totalSale[$createdAt]['sub_total'] +=  $totalOrder['sub_total'];
                        $totalSale[$createdAt]['discount'] +=  $discount;
                        $totalSale[$createdAt]['offer'] +=  $totalOrder['offer'];
                        $totalSale[$createdAt]['tax_collected'] +=  $totalOrder['tax_collected'];
                    }
                }
                $totalSale =  array_values($totalSale);
                $new_published = [];
                foreach($totalSale as $key => $row){
                    $new_published[$key] = $row['created_at'];
                }
                if(!empty($totalSale)){
                    array_multisort($new_published, SORT_ASC, $totalSale);
                }
                $sheet->loadView(
                    'report.total-sale-excel',
                    ['totalSale' => $totalSale, 'franchise' => $franchise,
                        'franchiseId' => $franchiseId, 'fromDate'=> $fromDate, 'toDate' => $toDate, 'users' => $users
                    ]
                );
            });
        })->export('xls');
    }
    /**
     * @param $franchiseId
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    private function itemSaleReport($franchiseId, $fromDate, $toDate)
    {
        $employees = Employee::whereIn('franchise_id', $franchiseId)->pluck('user_id')->toArray();
        $orders =  Order::with('orderDetail.product', 'orderDetail.product.productPrice', 'employee')
            ->whereCancelledBy(null);
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        if(!empty($franchiseId)) {
            $orders = $orders->whereIn('order_taken_by', $employees);
        }
        if (!empty($fromDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), '>=', userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $orders = $orders->whereIn('order_taken_by', $employees)
                ->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"),'<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $orders = $orders->where(\DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }        
        $orders = $orders->get()->toArray();
        $productId = $quantity = $viewOrder = $detail = [];
        $total = [];
        foreach ($orders as $order) {
            foreach ($order['order_detail'] as $orderDetail) {
                if ($orderDetail['removed_by'] == null) {
                    $productId[] = $orderDetail['product_id'];
                    $quantity[] = $orderDetail['quantity'];
                    $total[] +=$orderDetail['grand_total'];
                    $detail[]  = [
                        'id' => $orderDetail['id'],
                        'product_id' => $orderDetail['product_id'],
                        'product_name' => $orderDetail['product']['name'],
                        'quantity' => $orderDetail['quantity'],
                        'sub_total' => $orderDetail['sub_total'],
                        'product_code' => $orderDetail['product']['product_code'],
                        'grand_total' => $orderDetail['grand_total'],
                    ];

                }
            }
        }
        $partnerSums=array();
        foreach($detail as $res)
        {
            if(!array_key_exists($res['product_id'], $partnerSums)){
                $partnerSums[$res['product_id']] = $res;
            } else {
                $partnerSums[$res['product_id']]['grand_total'] += $res['grand_total'];
                $partnerSums[$res['product_id']]['quantity'] += $res['quantity'];
            }
        }
        $partnerSums = array_values($partnerSums);
        foreach($partnerSums as $key => $row){
            if ($total) {
                $percentage = round(($row['grand_total'] / array_sum($total)) * 100, 2);
                $viewOrder[] = [
                    'id' => $row['id'],
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'sub_total' => $row['sub_total'],
                    'grand_total' => $row['grand_total'],
                    'product_code' => $row['product_code'],
                    'percent' => $percentage,
                    'total' => $total,
                ];
            }
        }
        foreach($viewOrder as $key => $row){
            $new_published[$key] = $row['percent'];
        }
        if(!empty($viewOrder)){
            array_multisort($new_published, SORT_DESC, $viewOrder);
        }
        return $viewOrder;
    }
    /**
     * @param $customer
     * @param $fromDate
     * @param $toDate
     * @return array
     * @author Parth Patel <parth.d9ithub@gmail.com> 
     */
    public function NFCBandReport()
    {
        $viewOrder=[];
        /*if (superAdmin()) {
            
        } else {
            $customerId = null;
        }*/
        $customerId = Input::get('customer');
        $bandId = Input::get('bands');
        $fromDate =  Input::get('from_date');
        $toDate =  Input::get('to_date');
        $customersList = Customer::get()->pluck('name','id')->toArray();
        $nfcBand = \Config::get('constants.nfcBandStatus');
        $band_detail = array();
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        
       /* if(empty($fromDate) and empty($toDate)){
            $fromDate =  date('d-M-y');
            $toDate =  date('d-M-y');
        }elseif (empty($fromDate)) {
            $fromDate =  date('d-M-y');
        }elseif (empty($toDate)) {
            $toDate =  date('d-M-y');
        }*/
        $i = 1;
        $j = 1;
        $k = 1;
        $l = 1;
        $totalBandsNew = 0;
        $totalBandsInUse = 0;
        $totalBandsDamaged = 0;
        $totalBandsLostBand = 0;
        $totalBands = array();
        $totalCount = DB::table('device_pool')->get();
        if (isset($totalCount) and count($totalCount) > 0) {
            
            foreach ($totalCount as $count => $countedData) {
                   // $devicePoolCheck = DB::table('device_pool')->where('id',$countedData->id)->first();
                if ($countedData->status == 1) {
                        $totalBandsNew = $i;
                        $i++;
                }
                elseif ($countedData->status == 2) {
                        $totalBandsInUse  = $j;
                        $j++;
                }elseif ($countedData->status == 3) {
                        $totalBandsDamaged = $k;
                        $k++;
                }elseif ($countedData->status == 4) {
                        $totalBandsLostBand = $l;
                        $l++;
                }
                $totalBands = [
                                'new'           => $totalBandsNew,
                                'in_use'        => $totalBandsInUse,
                                'damaged'       => $totalBandsDamaged,
                                'lost'          => $totalBandsLostBand,
                                'total_count'   => count($totalCount),
                            ];
            }
        }
        $check_band = DB::table('customer_device')
        ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id');
        if(!empty($customerId)) {
            $check_band = $check_band->where('customer_device.customer_id',$customerId);
        }
        if (!empty($bandId)) {
            $check_band = $check_band->where('device_pool.status',$bandId);
        }
        if(!empty($fromDate)) {
            $check_band = $check_band->where('customer_device.issued_at','>=',userTimeToUTC($fromDate));
        }
        if (!empty($toDate)) {
            $check_band = $check_band->where('customer_device.issued_at','<=',userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $check_band = $check_band->where(\DB::raw("(DATE_FORMAT(customer_device.issued_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        } 
        $check_band =$check_band->groupBy('device_pool.id')
        ->select('device_pool.created_at','device_pool.updated_at','device_pool.status','customer_device.is_active','customer_device.issued_at','customer_device.customer_id','device_pool.original_UUID')
        ->get();
        $i=0;
        if(isset($check_band) and count($check_band) > 0){
            foreach ($check_band as $keys => $band) {
            if($band->customer_id == null){
                $customer_data= [];
            }else{
                $customer_data=Customer::where('id',$band->customer_id)->where('is_active',1)->first()->toArray();
            }             
                $band_detail[$i]['original_UUID'] = $band->original_UUID;
                $band_detail[$i]['issued_at'] = 
                \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $band->issued_at, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                if(count($customer_data) > 0){
                    if($band->status == 1){
                        $band_detail[$i]['customer_name'] = '-';
                     $band_detail[$i]['customer_mobile'] = '-';    
                    }else{
                        $band_detail[$i]['customer_name'] = $customer_data['name'];
                        $band_detail[$i]['customer_mobile'] = $customer_data['contact_number'];
                    }
                }else{
                    $band_detail[$i]['customer_name'] = '-';
                     $band_detail[$i]['customer_mobile'] = '-';
                }
                if ($band->status == 1) {
                    $band_detail[$i]['issued_at'] = '-';
                }
                if($band->is_active == 1){
                    $band_detail[$i]['is_active'] = \Lang::get('views.active');
                } elseif ($band->status == 1) {
                    $band_detail[$i]['is_active'] = '-';
                } else{
                    $band_detail[$i]['is_active'] = \Lang::get('views.inactive');
                }
                if($band->status == 1){
                    $band_detail[$i]['status'] = \Lang::get('views.new');
                    $i++;
                }
                elseif($band->status == 2){
                    $band_detail[$i]['status'] = \Lang::get('views.in_use');
                    $i++;
                }
                elseif($band->status == 3){
                    $band_detail[$i]['status'] = \Lang::get('views.damaged');
                    $i++;
                }
                elseif($band->status == 4){
                    $band_detail[$i]['status'] = \Lang::get('views.lost');
                    $i++;
                }
            }
        }else{
            $band_detail = [];
        }
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $itemCollection = collect($band_detail);
                $perPage = 10;
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
                $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        if(empty(Input::get('from_date'))){
            $fromDate = Input::get('from_date');
        }
        if (empty(Input::get('to_date'))) {
            $toDate = Input::get('to_date');
        }        
        return view('report.nfc_band_report',compact('viewOrder','customers','customerId','nfcBand','customersList','band_detail','bandId','paginatedItems','fromDate','toDate','totalBands'));
    }
    /**
     * @param $customer
     * @return array
     * @author Parth Patel <parth.d9ithub@gmail.com> 
     * create excel based on report
     */
    public function CustoemrReportExcel()
    {
        $filename = 'CustoemrReport'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {
            $excel->sheet($filename, function ($sheet) {
                $customerId = Input::get('customerId');
                $bandId =  Input::get('bandId');
                $fromDate =  Input::get('from_date');
                $toDate =  Input::get('to_date');
                //$whereFranchiseId = !$customerId ? array_keys($franchise) : [$customerId];
                $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
                $band_detail = array();
                    $check_band = DB::table('customer_device')
                ->leftjoin('device_pool','customer_device.device_pool_id','=','device_pool.id');
                if(!empty($customerId)) {
                    $check_band = $check_band->where('customer_device.customer_id',$customerId);
                }
                if (!empty($bandId)) {
                    $check_band = $check_band->where('device_pool.status',$bandId);
                }
                if(!empty($fromDate)) {
                    $check_band = $check_band->where('customer_device.issued_at','>=',userTimeToUTC($fromDate));
                }
                if (!empty($toDate)) {
                    $check_band = $check_band->where('customer_device.issued_at','<=',userTimeToUTC($toDate));
                }
                if (true === $noDateFilter) {
                    $check_band = $check_band->where(\DB::raw("(DATE_FORMAT(customer_device.issued_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
                } 
                $check_band =$check_band->groupBy('device_pool.id')
                ->select('device_pool.created_at','device_pool.updated_at','device_pool.status','customer_device.is_active','customer_device.issued_at','customer_device.customer_id','device_pool.original_UUID')
                ->get();
        $i=0;
        if(isset($check_band) and count($check_band) > 0){
            foreach ($check_band as $keys => $band) {
                $customer_data=Customer::where('id',$band->customer_id)->where('is_active',1)->first()->toArray();
                $band_detail[$i]['original_UUID'] = $band->original_UUID;
                $band_detail[$i]['issued_at'] =  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $band->issued_at, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                if(count($customer_data) > 0){
                    $band_detail[$i]['customer_name'] = $customer_data['name'];
                    $band_detail[$i]['customer_mobile'] = $customer_data['contact_number'];
                }else{
                    $band_detail[$i]['customer_name'] = '-';
                    $band_detail[$i]['customer_mobile'] = '-';
                }
                if($band->is_active == 1){
                    $band_detail[$i]['is_active'] = \Lang::get('views.active');
                }
                else{
                    $band_detail[$i]['is_active'] = \Lang::get('views.inactive');
                }
                if($band->status == 1){
                    $band_detail[$i]['status'] = \Lang::get('views.new');
                    $i++;
                }
                elseif($band->status == 2){
                    $band_detail[$i]['status'] = \Lang::get('views.in_use');
                    $i++;
                }
                elseif($band->status == 3){
                    $band_detail[$i]['status'] = \Lang::get('views.damaged');
                    $i++;
                }
                elseif($band->status == 4){
                    $band_detail[$i]['status'] = \Lang::get('views.lost');
                    $i++;
                }
            }
        }else{
            $band_detail = [];
        }
                $sheet->loadView(
                    'report.nfs-band-report',
                    ['band_detail' => $band_detail]
                );
            });
        })->export('xls');
    }
    /**
     * get report of customer wallet history
     *
     * @param  customer_id ,from_date,to_date
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */

    public function WalletHistory()
    {
        $customerId = Input::get('customer');
        $fromDate =  Input::get('from_date');
        $toDate =  Input::get('to_date');
        $customersList = Customer::pluck('name','id')->toArray();
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        $credit = 0;
        $debit = 0;
        $wallet = CustomerWalletHistory::join('customer_device','customer_wallet_history.customer_device_id','=','customer_device.id')
        ->join('customers', 'customers.id', '=', 'customer_device.customer_id');
        if (!empty($customerId)) {
            $wallet = $wallet->where('customer_device.customer_id',$customerId);
        } 

        if (!empty($fromDate)){
            $wallet = $wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d %H:%i:%s'))"), '>=', userTimeToUTC($fromDate));
        }

        if(!empty($toDate)) {
            $wallet =$wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d %H:%i:%s'))"), '<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $wallet = $wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }
        $wallet = $wallet//->groupBy('customer_wallet_history.id')
              ->select('customer_wallet_history.created_at','customer_wallet_history.credit_amount','customer_wallet_history.payment_mode','customer_wallet_history.debit_amount','customer_wallet_history.comment','customer_wallet_history.customer_device_id','customer_device.customer_id', 'customers.name AS customer_name', 'customers.contact_number AS customer_mobile')
              ->get();
        $walletData=array();
        //dd($wallet->toArray());
        foreach ($wallet->toArray() as $key => $value) {
            $value['wallet_balance'] = $this->getBalance($value['customer_id'], $value['created_at']);
           // dd($value['created_at']);
            if($value['payment_mode'] == 1){
                $value['payment_mode'] = \Lang::get('views.cash');
            }elseif($value['payment_mode'] == 2){
                $value['payment_mode'] = \Lang::get('views.card');
            }elseif($value['payment_mode'] == 3){
                $value['payment_mode'] = \Lang::get('views.paytm');
            }elseif($value['payment_mode'] == 0){
                $value['payment_mode'] = '-';
            }
            $walletData[] = $value;
        }     
        if(empty(Input::get('from_date'))){
            $fromDate = Input::get('from_date');
        }
        if (empty(Input::get('to_date'))) {
            $toDate = Input::get('to_date');
        }
         $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $itemCollection = collect($walletData);
                $perPage = 10;
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
                $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        return view('report.customer_wallet_history_report',compact('customersList','customerId','fromDate','toDate','walletData','paginatedItems'));
    }
    /**
     * get Excel report of customer wallet history
     *
     * @param  customer_id ,from_date,to_date
     * @return \Illuminate\Http\Response
     * @author Parth Patel <parth.d9ithub@gmail.com>
     */


    public function CustoemrWalletHistoryExcel()
    {
        $filename = 'WalletHistoryReport'.date('dMy');
        Excel::create($filename, function ($excel) use ($filename) {
            $excel->sheet($filename, function ($sheet) {
              $customerId = Input::get('customer');
        $fromDate =  Input::get('from_date');
        $toDate =  Input::get('to_date');
        $customersList = Customer::pluck('name','id')->toArray();
        $noDateFilter = !empty($fromDate) || !empty($toDate) ? false : true;
        $credit = 0;
        $debit = 0;
        $wallet = CustomerWalletHistory::join('customer_device','customer_wallet_history.customer_device_id','=','customer_device.id')
        ->join('customers', 'customers.id', '=', 'customer_device.customer_id');
        if (!empty($customerId)) {
            $wallet = $wallet->where('customer_device.customer_id',$customerId);
        } 

        if (!empty($fromDate)){
            $wallet = $wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d %H:%i:%s'))"), '>=', userTimeToUTC($fromDate));
        }

        if(!empty($toDate)) {
            $wallet =$wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d %H:%i:%s'))"), '<=', userTimeToUTC($toDate));
        }
        if (true === $noDateFilter) {
            $wallet = $wallet->where(\DB::raw("(DATE_FORMAT(customer_wallet_history.created_at,'%Y-%m-%d'))"),'=', date('Y-m-d'));
        }
        $wallet = $wallet//->groupBy('customer_wallet_history.id')
              ->select('customer_wallet_history.created_at','customer_wallet_history.credit_amount','customer_wallet_history.payment_mode','customer_wallet_history.debit_amount','customer_wallet_history.comment','customer_wallet_history.customer_device_id','customer_device.customer_id', 'customers.name AS customer_name', 'customers.contact_number AS customer_mobile')
              ->get();
        $walletData=array();
        foreach ($wallet->toArray() as $key => $value) {
            $value['wallet_balance'] = $this->getBalance($value['customer_id'], $value['created_at']);
            if($value['payment_mode'] == 1){
                $value['payment_mode'] = \Lang::get('views.cash');
            }elseif($value['payment_mode'] == 2){
                $value['payment_mode'] = \Lang::get('views.card');
            }elseif($value['payment_mode'] == 3){
                $value['payment_mode'] = \Lang::get('views.paytm');
            }elseif($value['payment_mode'] == 0){
                $value['payment_mode'] = '-';
            }
            $walletData[] = $value;
        }     
        if(empty(Input::get('from_date'))){
            $fromDate = Input::get('from_date');
        }
        if (empty(Input::get('to_date'))) {
            $toDate = Input::get('to_date');
        }
         $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $itemCollection = collect($walletData);
                $perPage = 10;
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
                $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
                $sheet->loadView(
                    'report.customer-wallet-history-report',
                    ['band_detail' => $paginatedItems]
                );
            });
        })->export('xls');
    }

    /**
     * Get customer balance 
     *
     * @param  customer_id ,tillDateTime
     * @return \Illuminate\Http\Response
     * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
     */
    private function getBalance($customerId, $tillDateTime = '') {
        $tillDateTime = $tillDateTime ? $tillDateTime : date('Y-m-d H:i:s');
        $customersHistory = CustomerWalletHistory::with('customerDevice.customer')->whereHas('customerDevice', function ($query) use($customerId) {
                $query->where('customer_device.customer_id', $customerId)->withTrashed();
        })->where('created_at', '<=',$tillDateTime)->get();
        $credit = $debit = 0;
       // dd($customersHistory);
        foreach ($customersHistory as $wallet) {
            $credit += $wallet->credit_amount;
            $debit += $wallet->debit_amount;
        }
        return round(($credit - $debit), 2);
    }
}
