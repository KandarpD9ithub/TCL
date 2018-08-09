<?php

/**
 * @package App/Http/Controllers
 *
 * @class ProductController
 *
 * @author Azim Khan <azim@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */

namespace App\Http\Controllers;
use App\ItemSale;
use Illuminate\Http\Request;
use Excel;
class ItemSalesController extends Controller {
	private $obj;
	function __construct(){
		$this->obj = new ItemSale();
	}

    public function index() {    	
    	$result = $this->obj->getTopLowSales();
    	return view('itemSales.index',$result);
    }

    public function categorywise(){      	
    	$result = $this->obj->categoryWise();
    	$category_result = $result[0];
    	$franchise_result = $result[1];
    	return view('itemSales.category',compact('category_result','franchise_result'));
    }

    public function listcategory(){
    	$result = $this->obj->listCategory(); 
    }

    public function filterByFranchise(){   
    	$franchise = $_GET['franchise'];	
    	$result = $this->obj->filterByFranchise($franchise);
    	return $result;
    }

    public function filterByFranchiseTop(){
    	$franchise = $_GET['franchise'];    	
    	return $result = $this->obj->getFilterReportByTopSales($franchise); 
    }



    public function filterByFranchiseLow(){
    	$franchise = $_GET['franchise'];
    	return $result = $this->obj->getFilterReportByLowSales($franchise); 
    }

    public function filterRecordByLowRange(){
    	$franchise = $_GET['franchise'];
    	$filterby = $_GET['filterby'];    	
    	$result = $this->obj->filterRecordByLowRange($franchise,$filterby);    	
    	return $result;
    }

    public function filterRecordByTopRange(){
    	$franchise = $_GET['franchise'];
    	$filterby = $_GET['filterby'];    	
    	$result = $this->obj->filterRecordByTopRange($franchise,$filterby);    	
    	return $result;
    }

    public function filterCategoryRecord(){  
    	$franchise = $_GET['franchise'];  	
    	$filterby = $_GET['filterby'];      	    	   
    	$result = $this->obj->getFilterReport($franchise,$filterby);     	
    	return $result;
    }

    public function excelTopSales($franchise,$filterby){	
        $export = $this->obj->filterRecordByTopRange($franchise, $filterby);        
        Excel::create('Top Sales',function($excel) use ($export){
            $excel->sheet('Sheet 1',function($sheet) use ($export){            	
            	$sheet->cell( 'A1', 'Item Name'); $sheet->cell( 'B1', 'SKU'); $sheet->cell( 'C1', 'Total Orders'); $sheet->cell( 'D1', 'Order Quantity'); $sheet->cell( 'E1', 'Gross Sale');
            	$i = 2;                       		   
            	foreach ($export as $key => $value) {
            		$sheet->cell( "A".$i, $value[0]); $sheet->cell( 'B'.$i, $value[1]); $sheet->cell( 'C'.$i, $value[2]);$sheet->cell( 'D'.$i, $value[3]); $sheet->cell( 'E'.$i, $value[4]);
            		$i++;
            	}
                //$sheet->fromArray($datasheet);
            });
        })->export('xlsx');
    }

    public function excelLowSales($franchise,$filterby){    	
        $export = $this->obj->filterRecordByLowRange($franchise,$filterby);
        Excel::create('Low Sales',function($excel) use ($export){        	
            $excel->sheet('Sheet 1',function($sheet) use ($export){
            	$sheet->cell( 'A1', 'Item Name'); $sheet->cell( 'B1', 'SKU'); $sheet->cell( 'C1', 'Total Orders'); $sheet->cell( 'D1', 'Order Quantity');$sheet->cell( 'E1', 'Gross Sale');       	
            	$i = 2;                       		    
            	foreach ($export as $key => $value) {
            		$sheet->cell( "A".$i, $value[0]); $sheet->cell('B'.$i, $value[1]); $sheet->cell( 'C'.$i, $value[2]); $sheet->cell('D'.$i, $value[3]); $sheet->cell( 'D'.$i, $value[4]);
            		$i++;
            	}
                //$sheet->fromArray($datasheet);
            });
        })->export('xlsx');
    }

    public function excel($franchise,$filterby){
        $export = $this->obj->getFilterReport($franchise,$filterby);
        Excel::create('Category Wise',function($excel) use ($export){        	
            $excel->sheet('Sheet 1',function($sheet) use ($export){
            	$sheet->cell( 'A1', 'Category'); $sheet->cell( 'B1', 'Total Products'); $sheet->cell( 'C1', 'Total Quantity');
            	$i = 2;            	    
            	foreach ($export as $key => $value) {
            		$sheet->cell( "A".$i, $key); $sheet->cell( 'B'.$i, $value[0]); $sheet->cell( 'C'.$i, $value[1]);
            		$i++;
            	}
                //$sheet->fromArray($datasheet);
            });
        })->export('xlsx');
    }

}
?>