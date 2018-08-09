Hello, <b> {{ ucfirst($user->name) }}, </b>
<p>
    This is an automated email confirming that your invoice with the following information is successfully paid.
</p>

<p>
	<table border="1" width="70%">
		<tr>
			<td width="50%">
				Order Number.: <b> {{ $user->order_number }} </b><br>
				Transaction Id.: <b> {{ $user->transactionId }} </b><br>
				<br>
				Customer Name: <b> {{ ucfirst($user->name) }} </b><br>
				Mobile: <b> {{ $user->contact_number }} </b><br>

			</td>
			<td width="50%" colspan="4" align="right">
				<?php
				$datea = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->order_date, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');

				?>
				Date & Time : <b> {{ $datea }} </b><br>
				<br>
				<br>
				Payment Status: <b> Paid </b>
			</td>
		</tr>
</table>
<table border="1" width="70%">
		<tr bgcolor="#ddd">
			<th width="20%"> S. No. </th>
			<th> Item </th>
			<th> Qty. </th>
			<th> Unit Price </th>
			<th> Amount </th>
		</tr>
		<?php 
			$subTotal = 0;
			$no = 1 ;
			$taxes = array();
			$i = 0;
			$taxRate=0;
		?>
		@foreach($orderDetails as $key => $order)
		<tr>
			<td > {{$no}}. </td>
			<td> {{ $order['product_name'] }} </td>
			<td> {{ $order['quantity'] }} </td>
			<td> {{ $order['unit_price'] }} </td>
			<td> {{ $order['amount'] }} </td>			
			<?php
				$no++;
			?>
		</tr>
		@endforeach
		<tr bgcolor="#ddd">
			<td> Sub Total </td>
			<td colspan="4" align="right"> {{ $user->subTotal }} </td>
		</tr>
		<tr>
			<td> Discount </td>
			<td colspan="4" align="right"> {{ $user->discount }} </td>
		</tr>
		<tr>
			<td> Offer </td>
			<td colspan="4" align="right"> {{ $user->offer }} </td>
		</tr>
		<tr>
			<td colspan="5" align="center"> 
			 {{ $user->serviceTax }}<br>
				@foreach($user->taxData as $taxKey => $taxValue)
				 		
				 		<?php
				 		//$taxRate +=$taxValue['tax_rate'];
				 		echo '(Tax-'.round($taxValue['tax_rate'], 2).'% '.$taxValue['type'] .') on '. $taxKey .' = '.number_format($taxValue['rate'], 2);
				 		?>
				 		<br>	
			  @endforeach</td>
			{{-- <td colspan="4" align="right"> {{ $user->taxTotal }} </td> --}}
		</tr>	
		<tr bgcolor="#ddd">
			<td><b> Grand Total </b></td>
			<td colspan="4" align="right"><b> {{$user->grandTotal }} </b></td>
		</tr>
	</table>
	<p><b>Payment type: {{ $user->payment_method }}</b></p>
	<p>
			Disclaimer:
	</p>
	<p>
		<ul>
		  <li>The statement mailed to you by TCL </li>
		  <li>All applicable taxes and levies as appearing on the website/mobile application/ email are being charged. </li>
		</ul> 
	</p>
</p>

<br>

<p>
<b>Thanks and regards</b> <br>

Customer Service <br>
The Theatre Club & Lounge, <br>
Radisson Blu Hotel, 2nd Floor, <br>
PaschimVihar, Delhi, India <br>
www.thetheatreclubandlounge.com<br>

</p>
