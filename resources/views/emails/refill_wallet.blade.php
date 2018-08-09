Hello, {{ ucfirst($user->name) }}
<p>
    This is an automated email confirming that band with following details has been successfully recharged.
</p> <br>
<p>

	<table border="1" width="50%">
		
		<tr>
			<td width="50%"> Customer Name </td>
			<td> <b> {{ ucfirst($user->name) }} </b></td>
		</tr>

		<tr>
			<td width="50%"> Mobile </td>
			<td> <b> {{ $user->contact_number }} </b></td>
		</tr>

		<tr>
			<td width="50%"> Refil Amount </td>
			<td> <b> INR {{ $user->refil_amount }}/- </b></td>
		</tr>

		<tr>
			<td width="50%"> Refilled Date/Time </td>
			
			<td> <b> {{ $user->created_at }} </b></td>
		</tr>

		<tr>
			<td width="50%"> Last Balance </td>
			<td> <b> INR {{ $user->last_balance }}/- </b></td>
		</tr>

		<tr>
			<td width="50%"> Current Balance </td>
			<td> <b> INR {{ $user->total }}/- </b></td>
		</tr>

	</table>
    
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

<!-- URL: <a href='{{ URL::to("/login") }}'>
    {{ URL::to("/login") }}
</a> -->
<br>
