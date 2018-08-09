<?php

namespace App\Http\Controllers;

use App\Country;
use App\Employee;
use App\Franchise;
use App\User;

class ProfileController extends Controller
{
    /**
     * @return mixed
     */
    function profile()
    {
        $user = User::whereId(\Auth::User()->id)->get();
        return response()->success(['user' => $user]);
    }

    /**
     * @return mixed
     */
    public function userFranchise()
    {
        $franchiseId= Employee::whereUserId(\Auth::user()->id)->first()->franchise_id;
        $franchiseData = Franchise::whereId($franchiseId)->first();
        $country = Country::whereId($franchiseData->country_id)->first()->name;
        $orderTakenBy = User::whereId(\Auth::user()->id)->first()->name;
        $storeManger = Employee::whereFranchiseId($franchiseId)->pluck('user_id');
        $storeMangerEmail = User::whereRoleName('4')->whereIn('id', $storeManger)->first()->email;
        $userFranchiseDetail = [
            'order_taken_by' => $orderTakenBy,
            'franchise_name' => $franchiseData->name,
            'address_line_one' => $franchiseData->address_line_one,
            'address_line_two' => $franchiseData->address_line_two,
            'city' => $franchiseData->city,
            'region' => $franchiseData->region,
            'country' => $country,
            'store_manager_email' => $storeMangerEmail,
            'gst_number' =>$franchiseData->gst_number
        ];
        return response()->success(['franchiseDetail' => $userFranchiseDetail]);
    }
}
