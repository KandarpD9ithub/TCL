<?php
/**
 * @package App
 *
 * @class ItemSales
 *
 * @author Azim Khan  <azim@surmountsoft.com>
 *
 * @copyright 2016 SurmountSoft Pvt. Ltd. All rights reserved.
 */
namespace App;
use App\Category AS category;
use App\Menu AS menu;
use App\Employee AS emp;
use App\Franchise AS franchise;
use App\ProductPrice AS productprice;
use Illuminate\Database\Eloquent\Model;
class ItemSale extends Model {
	public function getTopLowSales(){			
			$productName = Product::pluck('name', 'id')->toArray();
			$topQuery = \DB::select('SELECT product_id, COUNT(product_id) as cnt, SUM(quantity) AS total FROM order_details GROUP BY product_id ORDER BY total DESC LIMIT 5');
			$lowQuery = \DB::select('SELECT product_id, COUNT(product_id) as cnt, SUM(quantity) AS total FROM order_details GROUP BY product_id ORDER BY total ASC LIMIT 5');    
			$topResult = [];      
			foreach ($productName as $productKey => $product) {
				foreach ($topQuery as $productSale) {
					if($productSale->product_id==$productKey){
						$productCode = $this->getProductCode($productKey);
						$productGross = $this->getProductGrossSale($productKey);
						array_push($topResult,array($productName[$productKey],$productCode,$productSale->cnt,$productSale->total,$productGross));
					}
				}
			}
			$lowResult = [];   
			foreach ($productName as $productKey => $product_value) {
				foreach ($lowQuery as $productSale) {    			
					if($productSale->product_id==$productKey){
						$productCode = $this->getProductCode($productKey);
						$productGross = $this->getProductGrossSale($productKey);
						array_push($lowResult,array($productName[$productKey],$productCode,$productSale->cnt,$productSale->total,$productGross));
					}
				}
			}
		    return array('sales'=>array('top_sales' => $topResult, 'low_sales' => $lowResult ,'franchise' => $this->getFranchises()));
	}

	private function getProductGrossSale($productId){

		$grossSale = NULL;
		$franchises = $this->getFranchises();		
		foreach ($franchises as $franchise => $val) {
			$employees = $this->getFranchiseEmployees($franchise);
			if(!empty($employees)){
				$productSoldQuantity = $this->getProductSoldQuantity($employees,$productId);				
				if(!empty($productSoldQuantity)){
					$productPrice = $this->productPrice($productId,$franchise);
					$taxDetails = $this->getFranchiseTaxDetails($franchise);
					$grossSale += $this->calculateProductGross($productPrice,$taxDetails,$productSoldQuantity);
				}
			}
		}
		return $grossSale;
	}

	private function calculateProductGross($productPrice,$taxDetails,$productSoldQuantity){
		$sale = $productPrice*$productSoldQuantity;
		if(isset($taxDetails[0]->tax_rate)){
			// Service Charge			
			return $sale += $sale/$taxDetails[0]->tax_rate;
		}else{
			return $sale;
		}		
	}

	private function getFranchiseTaxDetails($franchiseId){
		return \DB::select("SELECT tax_name, tax_rate FROM taxes WHERE franchise_id IN($franchiseId)");
	}

	private function getProductSoldQuantity($employees,$productId,$range=''){		
		if($range==''){
			$ordersList = \DB::select("SELECT GROUP_CONCAT(id) AS orderlist FROM orders WHERE order_taken_by IN ($employees) AND status = 'delivered'");
		}elseif($range=='daily'){			
			$ordersList = \DB::select("SELECT GROUP_CONCAT(id) AS orderlist FROM orders WHERE order_taken_by IN ($employees) AND status = 'delivered' AND DATE_FORMAT(created_at,'%Y-%m-%d') = CURDATE()");
		}elseif($range=='weekly'){
			$query = "SELECT GROUP_CONCAT(id) AS orderlist FROM orders WHERE order_taken_by IN ($employees) AND status = 'delivered' AND created_at BETWEEN DATE_SUB(NOW(),INTERVAL 1 WEEK) AND NOW()";			
			$ordersList = \DB::select($query);
		}else{
			$ordersList = \DB::select("SELECT GROUP_CONCAT(id) AS orderlist FROM orders WHERE order_taken_by IN ($employees) AND status = 'delivered' AND created_at BETWEEN DATE_SUB(NOW(),INTERVAL 1 MONTH) AND NOW()");
		}
		$orderlist = $ordersList[0]->orderlist;
		$query = "SELECT SUM(quantity) AS qty FROM order_details WHERE order_id IN ($orderlist) AND product_id = $productId";		
		$qty = \DB::select($query);		
		return $qty[0]->qty;
	}

	private function productPrice($productId,$franchiseId){
		$price = \DB::select("SELECT price FROM product_prices WHERE product_id = $productId AND franchise_id = $franchiseId");		
		if(!empty($price[0]->price)){			
			return $price[0]->price;
		}else{
			$price = \DB::select("SELECT price FROM products WHERE id = $productId AND is_active = 1");			
			return $price[0]->price;
		}
	}

	public function getFilterReportByTopSales($franchise){
		$employees = $this->getFranchiseEmployees($franchise);
		$orderId = \DB::select("SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees)"); 
		$orders = $orderId[0]->orders; // getting order ids		
		$query = "SELECT product_id, COUNT(product_id) as cnt, SUM(quantity) AS total FROM order_details WHERE product_id in ($orders) GROUP BY product_id ORDER BY total DESC LIMIT 5";		
		$details = \DB::select($query);

		$topResult = [];   
			foreach ($details as $product_key => $product_value) {				
				array_push($topResult,array($this->getProductName($product_value->product_id),$this->getProductCode($product_value->product_id),$product_value->cnt,$product_value->total,$this->getProductGrossSale($product_value->product_id)));	
			}		
		return $topResult;
	}

	public function getFilterReportByLowSales($franchise){
		$employees = $this->getFranchiseEmployees($franchise);
		$orderId = \DB::select("SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees)"); 
		$orders = $orderId[0]->orders; // getting order ids		
		$query = "SELECT product_id, COUNT(product_id) as cnt, SUM(quantity) AS total FROM order_details WHERE product_id in ($orders) GROUP BY product_id ORDER BY total ASC LIMIT 5";		
		$details = \DB::select($query);
		$lowSales = [];
			foreach ($details as $productKey => $productValue) {
				array_push($lowSales,array($this->getProductName($productValue->product_id),$this->getProductCode($productValue->product_id),$productValue->cnt,$productValue->total,$this->getProductGrossSale($productValue->product_id)));
			}				
		return $lowSales;
	}

	public function filterRecordByLowRange($franchise,$filterBy){
		$employees = $this->getFranchiseEmployees($franchise);
		$orders = $this->filterByRange($employees,$filterBy);
		$query = "SELECT od.product_id, COUNT(od.product_id) AS cnt, SUM(od.quantity) total FROM order_details od WHERE order_id in($orders) GROUP BY product_id ORDER BY total ASC LIMIT 5";		
		$details = \DB::select($query);		
		$lowResult = [];   
			foreach ($details as $product_key => $product_value) {
				$productPrice = $this->productPrice($product_value->product_id,$franchise);	//getting product price
				$taxDetails = $this->getFranchiseTaxDetails($franchise); // getting tax details
				$productSoldQuantity = $this->getProductSoldQuantity($employees,$product_value->product_id,$filterBy);
				$grossSale = $this->calculateProductGross($productPrice,$taxDetails,$productSoldQuantity);	
				array_push($lowResult,array($this->getProductName($product_value->product_id),$this->getProductCode($product_value->product_id),$product_value->cnt,$product_value->total,$grossSale));				
			}				
		return $lowResult;
	}

	public function filterRecordByTopRange($franchise,$filterby){
		$employees = $this->getFranchiseEmployees($franchise);		
		$orders = $this->filterByRange($employees,$filterby);				
		$query = "SELECT od.product_id, COUNT(od.product_id) AS cnt, SUM(od.quantity) total FROM order_details od WHERE order_id in($orders) GROUP BY product_id ORDER BY total DESC LIMIT 5";		
		$details = \DB::select($query);
		$topResult = [];   
		$grossSale = NULL;
			foreach ($details as $product_key => $product_value) {

				$productPrice = $this->productPrice($product_value->product_id,$franchise);	//getting product price
				$taxDetails = $this->getFranchiseTaxDetails($franchise); // getting tax details
				$productSoldQuantity = $this->getProductSoldQuantity($employees,$product_value->product_id,$filterby);
				$grossSale = $this->calculateProductGross($productPrice,$taxDetails,$productSoldQuantity);											
				array_push($topResult,array($this->getProductName($product_value->product_id),$this->getProductCode($product_value->product_id),$product_value->cnt,$product_value->total,$grossSale));				
			}			
		return $topResult;
	}

	public function categoryWise(){	

		$result = \DB::table('order_details')
					->join('menu', 'order_details.product_id','=','menu.product_id')
					->join('categories', 'categories.id','=','menu.category_id')
    				->select('categories.name', \DB::raw('COUNT(order_details.product_id) AS cnt'), \DB::raw('SUM(order_details.quantity) AS quantity'))
					->groupBy('menu.category_id', 'categories.name')
					->get();			
					return array($result,$this->getFranchises());
	}	

	public function getFranchises(){
		$result = franchise::pluck('name', 'id');				
		return $result;
	}	

	public function filterByFranchise($franchise){		
		$employees = $this->getFranchiseEmployees($franchise);
		$orderId = \DB::select("SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees)"); 
		$orders = $orderId[0]->orders; // getting order ids
		$details = \DB::select("SELECT  od.product_id, COUNT(od.product_id) AS total_order, SUM(od.quantity) total_qty FROM order_details od WHERE order_id in($orders) GROUP BY product_id");		

		$categorywise = array();
		foreach ($details as $key => $value) {			
			if(!in_array($this->getCategoryName($value->product_id), $categorywise)){
				$productPrice = $this->productPrice($value->product_id,$franchise);	//getting product price
				$taxDetails = $this->getFranchiseTaxDetails($franchise); // getting tax details
				$productSoldQuantity = $this->getProductSoldQuantity($employees,$value->product_id);
				$grossSale = $this->calculateProductGross($productPrice,$taxDetails,$productSoldQuantity);				
				$categorywise[$this->getCategoryName($value->product_id)] = array($value->total_order,$value->total_qty,$grossSale);
			}			
		}
		return $categorywise;
	}

	private function getFranchiseEmployees($franchise){
		$employees = \DB::select("SELECT GROUP_CONCAT(user_id) AS user_id FROM employees WHERE franchise_id = $franchise");
		return $employees = $employees[0]->user_id;
	}

	private function getCategoryName($prduct_id){		
		$categoryId = \DB::table('menu')->WHERE('product_id', $prduct_id)->value('category_id');
		$categoryName = \DB::table('categories')->WHERE('id', $categoryId)->value('name');		
		return $categoryName;
	}

	private function getProductName($id){
		$productName = \DB::table('products')->WHERE('id', $id)->value('name');		
		return $productName;
	}

	private function getProductCode($id){
		$productCode = \DB::table('products')->WHERE('id', $id)->value('product_code');		
		return $productCode;
	}

	public function getFilterReport($franchise,$filterby){		    	
		$employees = $this->getFranchiseEmployees($franchise);
		$orders = $this->filterByRange($employees,$filterby);
		$details = \DB::select("SELECT od.product_id, COUNT(od.product_id) AS total_order, SUM(od.quantity) total_qty FROM order_details od WHERE order_id in($orders) GROUP BY product_id");
		$day = array();
		foreach ($details as $key => $value) {			
			if(!in_array($this->getCategoryName($value->product_id), $day)){
				$productPrice = $this->productPrice($value->product_id,$franchise);	//getting product price
				$taxDetails = $this->getFranchiseTaxDetails($franchise); // getting tax details
				$productSoldQuantity = $this->getProductSoldQuantity($employees,$value->product_id);
				$grossSale = $this->calculateProductGross($productPrice,$taxDetails,$productSoldQuantity);
				error_log("Product Price :".$value->product_id .$productPrice.' sold'.$productSoldQuantity.' gross'.$grossSale,0);
				$day[$this->getCategoryName($value->product_id)] = array($value->total_order,$value->total_qty,$grossSale);
			}			
		}		
		return $day;			
	}

	public function filterByRange($employees,$filterBy){		
		if($filterBy=='daily'){				
			$query = "SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees) AND status = 'delivered' AND DATE_FORMAT(created_at,'%Y-%m-%d') = CURDATE()";							
			$order_id = \DB::select($query);
		}elseif ($filterBy=='weekly') {
			$query = "SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees) AND status = 'delivered' AND created_at BETWEEN DATE_SUB(NOW(),INTERVAL 1 WEEK) AND NOW()";			
			$order_id = \DB::select($query);
		}elseif ($filterBy=='monthly') {			
			$query = "SELECT GROUP_CONCAT(id) AS orders FROM orders WHERE order_taken_by IN($employees) AND status = 'delivered' AND created_at BETWEEN DATE_SUB(NOW(),INTERVAL 1 MONTH) AND NOW()";			
			$order_id = \DB::select($query);
		}		
		return $order_id[0]->orders;
	}

}
