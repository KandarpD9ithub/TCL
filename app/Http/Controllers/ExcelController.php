<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
class ExcelController extends Controller
{
    public function excel()
    {
        $export=["aaaaaa",'aaaaaa','adddddd'];
        Excel::create('Export Data',function($excel) use ($export){
            $excel->sheet('Sheet 1',function($sheet) use ($export){
                $sheet->fromArray($export);
            });
        })->export('xlsx');
    }
}
