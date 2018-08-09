<?php
/**
 * @package App/Helper
 *
 * @file Helper.php
 *
 * @description Common functions using throughout the application
 *
 * @author Ritu Slaria <ritu.slaria@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
use App\DiscountOfferRule;
use App\Menu;

/**
 * Delete method
 *
 * @param $routeParams
 * @param string $label
 * @param bool|false $iconInside
 * @param string $style
 * @return string
 */
function deleteForm($routeParams, $label = 'Delete', $iconInside = false, $style = "")
{
    /**
     * If @var: iconInside is set true,
     * then using label & submit button id unique (in case of two forms on same view)
     */
    $id = uniqid();
    $form = Form::open([
        'method'    => 'DELETE',
        'url'       => URL::to($routeParams),
        'onsubmit'  => 'return confirm(\'Do you really want to delete this?\')'
    ]);

    if ($iconInside === true) {
        $form .= '<label for="'.$id.'" class=" btn btn-xs btn-default delete-action '.$style.' ">
        <i class="fa fa-times-circle-o"></i> '.$label.'</label>';
        $form .= Form::submit($label, ['class' => 'hide ', 'id' => $id]);
    } else {
        $form .= Form::submit($label, ['class' => 'btn btn-xs btn-default delete-action '.$style.'']);
    }

    return $form .= Form::close();
}

/**
 * @return bool
 */
function superAdmin()
{
    if (\Auth::User()->role_name == '1') {
        return true;
    } else {
        return false;
    }
}

function offerAmount($productId, $totalPrice, $createdAt = null)
{
    $offers = DiscountOfferRule::whereRuleType('offer')->where('is_active', '1')
        ->where(function ($query) use($createdAt) {
            $query->where('from_date', '<=', $createdAt)->where('to_date', '>=', $createdAt);
        })->get()->toArray();
    if (!empty($offers)) {
        $categories = getCategories($productId);
        $offerDiscount = [];
        foreach ($offers as $offer) {
            $amount = $offer['amount'];
            $amountType = $offer['amount_type'];
            $condition = json_decode($offer['conditions'], true);
            $type = $condition['type'];
            if ($type == 'products') {
                $id = $condition['ids'];
                if(is_array($productId)) {
                    $productExists = array_intersect($productId, $id);
                } else {
                    $productExists = in_array($productId, $id);
                }
                if (empty($productExists)) {
                    $offerDiscount[]='';
                } else {
                    if ($amountType == 'fixed') {
                        $offerDiscount[] = $amount;
                    } elseif($amountType == 'percent') {
                        $offerDiscount[] = ($totalPrice*$amount)/100;
                    } else {
                        $offerDiscount[] = ($totalPrice*$amount);
                    }
                }
            } elseif ($type =='all') {
                if ($amountType == 'fixed') {
                    $offerDiscount[] = $amount;
                } elseif($amountType == 'percent') {
                    $offerDiscount[] = ($totalPrice*$amount)/100;
                } else {
                    $offerDiscount[] = ($totalPrice*$amount);
                }
            } else {
                $id = $condition['ids'];
                if(is_array($categories)) {
                    $categoryExists = array_intersect($categories, $id);
                } else {
                    $categoryExists = in_array($categories, $id);
                }
                if (empty($categoryExists)) {
                    $offerDiscount[]='';
                } else {
                    if ($amountType == 'fixed') {
                        $offerDiscount[] = $amount;
                    } elseif($amountType == 'percent') {
                        $offerDiscount[] = ($totalPrice*$amount)/100;
                    } else {
                        $offerDiscount[] = ($totalPrice*$amount);
                    }
                }
            }
        }
        $offerAmount = array_sum($offerDiscount);
        return $offerAmount;
    }
}

function discountAmount($productId, $totalPrice, $createdAt = null)
{
    $offers = DiscountOfferRule::whereRuleType('discount')->where('is_active', '1')
        ->where(function ($query) use($createdAt) {
            $query->where('from_date', '<=', $createdAt)->where('to_date', '>=', $createdAt);
        })->get()->toArray();
    if (!empty($offers)) {
        $categoryId = getCategories($productId);
        $offerDiscount = [];
        foreach ($offers as $offer) {
            $amount = $offer['amount'];
            $amountType = $offer['amount_type'];
            $condition = json_decode($offer['conditions'], true);
            $type = $condition['type'];
            if ($type == 'products') {
                $id = $condition['ids'];
                if(is_array($productId)) {
                    $productExists = array_intersect($productId, $id);
                } else {
                    $productExists = in_array($productId, $id);
                }

                if (empty($productExists)) {
                    $offerDiscount[]='';
                } else {
                    if ($amountType == 'fixed') {
                        $offerDiscount[] = $amount;
                    } else {
                        $offerDiscount[] = ($totalPrice*$amount)/100;
                    }
                }
            } elseif ($type =='all') {
                if ($amountType == 'fixed') {
                    $offerDiscount[] = $amount;
                } else {
                    $offerDiscount[] = ($totalPrice*$amount)/100;
                }
            } else {
                $id = $condition['ids'];
                if(is_array($categoryId)) {
                    $categoryExists = array_intersect($categoryId, $id);
                } else {
                    $categoryExists = in_array($categoryId, $id);
                }

                if (empty($categoryExists)) {
                    $offerDiscount[]='';
                } else {
                    if ($amountType == 'fixed') {
                        $offerDiscount[] = $amount;
                    } else {
                        $offerDiscount[] = ($totalPrice*$amount)/100;
                    }
                }
            }
        }
        $discountAmount = array_sum($offerDiscount);
        return $discountAmount;
    }
}

/**
 * @param array $productTotal
 * @param array $productId
 * @return mixed
 */
function getTaxes($productId, $totalPrice, $createdAt = null)
{
    $discount = '';
    $franchiseId= \App\Employee::whereUserId(Auth::user()->id)->first()->franchise_id;
    $taxes = \App\Tax::whereFranchiseId($franchiseId)->pluck('tax_rate', 'tax_name')->toArray();
    $offerAmount = offerAmount($productId, $totalPrice, $createdAt);
    $discountAmount = discountAmount($productId, $totalPrice, $createdAt);
    /*if (isset($input['discount'])) {
        $discountType = DiscountOfferRule::whereId($input['discount'])->where('is_active', '1')->first()->amount_type;
        $discountAmount = DiscountOfferRule::whereId($input['discount'])->where('is_active', '1')->first()->amount;
        if ($discountType == 'percent') {
            $discount = ($totalPrice*$discountAmount)/100;
        } else {
            $discount = $discountAmount;
        }
    }
    if (isset($input['discountPercent'])) {
        $discount = ($totalPrice*$input['discountPercent'])/100;
    }*/
    $totalPrices = $totalPrice - $discountAmount -$offerAmount;
    /*$serviceCharge = $vat = $serviceTax = $serviceChargeAmount =  $vatAmount = $serviceTaxAmount = 0;*/
    $sgst = $cgst = $sgstAmount = $cgstAmount = 0;
   /* if (isset($taxes['Service Charge'])) {
        $serviceCharge = round(($totalPrices * $taxes['Service Charge']) / 100, 2);
        $serviceChargeAmount = round($taxes['Service Charge'], 2);
    }
    $subTotal = $totalPrices + $serviceCharge;
    if (isset($taxes['VAT'])) {
        $vat = round(($subTotal * $taxes['VAT']) / 100, 2);
        $vatAmount = round($taxes['VAT'], 2);
    }
    if (isset($taxes['Service Tax'])) {
        $serviceTax = round(($subTotal * $taxes['Service Tax']) / 100, 2);
        $serviceTaxAmount = round($taxes['Service Tax'], 2);
    }*/
    if (isset($taxes['SGST'])) {
        $sgst = round(($totalPrices * $taxes['SGST']) / 100, 2);
        $sgstAmount = round($taxes['SGST'], 2);
    }
    if (isset($taxes['CGST'])) {
        $cgst = round(($totalPrices * $taxes['CGST']) / 100, 2);
        $cgstAmount = round($taxes['CGST'], 2);
    }
    /*$billAmount = round($totalPrices + $serviceTax + $serviceCharge + $vat);*/
    $billAmount = round($totalPrices + $sgst + $cgst);
    $totalAmount = [
        'sgst_amount' => $sgstAmount,
        'cgst_amount' => $cgstAmount,
        'subtotal' => $totalPrice,
        'discount'  => $discountAmount,
        'offer' =>$offerAmount,
        'sgst' => $sgst,
        'cgst' => $cgst,
        'grand_total' => $billAmount
    ];
    return $totalAmount;
}

/**
 * Merges two arrays for same keys
 * @param $Arr1
 * @param $Arr2
 * @return mixed
 */
function MergeArrays($Arr1, $Arr2)
{
    foreach ($Arr2 as $key => $Value) {
        if (array_key_exists($key, $Arr1) && is_array($Value)) {
            $Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);
        } else {
            $Arr1[$key] = $Value;
        }
    }
    return $Arr1;

}

/**
 * Generate Random password for users
 *
 * @param int $length
 * @return string
 */
function randomPassword($length = 8)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

/**
 * Check store manager role
 *
 * @return bool
 */
function storeManger()
{
    if (\Auth::User()->role_name == '4') {
        return true;
    } else {
        return false;
    }
}
/**
 * Check accountant role
 *
 * @return bool
 */
function accountant()
{
    if (\Auth::User()->role_name == '2') {
        return true;
    } else {
        return false;
    }
}
/**
 * custom pagination
 *
 * @param array $data
 * @return \Illuminate\Pagination\LengthAwarePaginator
 */
function customPaginate($data = array())
{
    $perPage = \Config::get('constants.RECORDS_PER_PAGE');
    $currentPage = Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
    $collection = new Illuminate\Support\Collection($data);
    $currentPageSearchResults = $collection->slice(($currentPage-1) * $perPage, $perPage)->all();
    $paginatedSearchResults= new Illuminate\Pagination\LengthAwarePaginator(
        $currentPageSearchResults, count($collection), $perPage
    );
    return $paginatedSearchResults;
}

function getCategories($productId)
{
    $categoryId = [];
    $categories = \App\Category::whereParentId(0)->get();
    $franchiseId = \App\Employee::where('user_id', \Auth::user()->id)->first()->franchise_id;
    $inactive = \App\InactiveMenuItems::where('franchise_id', $franchiseId)->pluck('menu_id')->toArray();
    if (is_array($productId)){
        foreach ($productId as $product) {
            $menuProducts = Menu::pluck('product_id')->toArray();
            if(in_array($product, $menuProducts)){
                foreach ($categories as $category) {
                    if ($category->is_active == 1) {
                        if ((count($category->child)>0) || (count($category->products)>0)) {
                            foreach ($category->products as $products) {
                                if ($products->is_active ==1) {
                                    if (!in_array($products->menu->id, $inactive)) {
                                        if ($products->id == $product) {
                                            $categoryId = $category->id;
                                        }
                                    }
                                }
                            }
                            foreach ($category->child as $subcategory) {
                                if ($subcategory->is_active == 1) {
                                    if (count($subcategory->products)>0) {
                                        foreach ($subcategory->products as $subProduct) {
                                            if ($subProduct->is_active ==1) {
                                                if (!in_array($subProduct->menu->id, $inactive)) {
                                                    if ($subProduct->id == $product) {
                                                        
                                                        $categoryId = $subcategory->id;
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
            }

        }
    } else {
        $menuProducts = Menu::pluck('product_id')->toArray();
        if (in_array($productId, $menuProducts)) {
            foreach ($categories as $category) {
                if ($category->is_active == 1) {
                    if ((count($category->child) > 0) || (count($category->products) > 0)) {
                        foreach ($category->products as $products) {
                            if ($products->is_active == 1) {
                                if (!in_array($products->menu->id, $inactive)) {
                                    if ($products->id == $productId) {
                                        $categoryId = $category->id;
                                    }
                                }
                            }
                        }
                        foreach ($category->child as $subcategory) {
                            if ($subcategory->is_active == 1) {
                                if (count($subcategory->products) > 0) {
                                    foreach ($subcategory->products as $subProduct) {
                                        if ($subProduct->is_active == 1) {
                                            if (!in_array($subProduct->menu->id, $inactive)) {
                                                if ($subProduct->id == $productId) {
                                                    $categoryId = $subcategory->id;
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
        }
    }
    return $categoryId;
}

/**
 * @return bool
 */
function storeManager()
{
    if (\Auth::User()->role_name == '4') {
        return true;
    } else {
        return false;
    }
}

function userTimeToUTC($dateTime){
  return  \Carbon\Carbon::createFromFormat('Y-m-d H:i',  date('Y-m-d H:i',strtotime($dateTime)), 'Asia/Kolkata')->setTimezone('UTC');
}