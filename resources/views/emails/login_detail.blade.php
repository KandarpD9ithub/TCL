Hi, {{ ucfirst($user->name) }}
<p>
    Your login details are as following:
</p> <br>
<p>
    E-mail Address : {{ $user->email }}<br>
    Password : {{ $password }}
</p>

<p>For security purposes, we recommend you to change your password.</p>

URL: <a href='{{ URL::to("/login") }}'>
    {{ URL::to("/login") }}
</a>
<br>
