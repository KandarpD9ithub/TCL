Hello, {{ ucfirst($user->name) }}
<p>
    This is an automated email confirming that band with following details has been successfully issued.
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
			<td width="50%"> Wallet Balance </td>
			<td> <b> INR {{ $user->total }}/- </b></td>
		</tr>

		<tr>
			<td width="50%"> Band issue date & time </td>
			<?php
			$date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->issued_at, 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
			?>
			<td> <b> {{ $date }} </b></td>
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
