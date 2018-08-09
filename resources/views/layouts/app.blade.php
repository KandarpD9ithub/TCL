<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TCL') }}</title>
    <link rel="icon" href="{{ asset('img/TLC_Logo-demo.png') }}" type="image/x-icon">
    <!-- Styles -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/multiple-select.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/jquery-ui-timepicker.css">
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">


    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand">
                       {{-- {{ config('app.name', 'TCL') }}--}}
                        <img src="{{ asset('img/TCL TEXT_ logo.png') }}" height="40" alt="TCL" class="navbar-brand"/>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            {{--<li><a href="{{ url('/register') }}">Register</a></li>--}}
                        @else
                            @if(superAdmin())
                                <li><a href="{{ url('/non-chargeable') }}">{{ Lang::get('views.non-chargeable') }}</a></li>
                                <li><a href="{{ url('/customer') }}">{{ Lang::get('views.customers') }}</a></li>
                                <li><a href="{{ url('/product') }}">{{ Lang::get('views.products') }}</a></li>
                                <li><a href="{{ url('/category') }}">{{ Lang::get('views.categories') }}</a></li>
                                <li><a href="{{ url('/franchise') }}">{{ Lang::get('views.franchise') }}</a></li>
                                <li><a href="{{ url('/rules') }}">{{ Lang::get('views.rules') }}</a></li>                                
                                <li><a href="{{ url('/employee') }}">{{ Lang::get('views.employees') }}</a></li>
                                <li><a href="{{ url('/taxes') }}">{{ Lang::get('views.taxes') }}</a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Reports <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('total/sale') }}">{{ Lang::get('views.sale') }}</a></li>
                                        {{--<li><a href="{{ url('transaction') }}">{{ Lang::get('views.transaction') }}</a></li>--}}
                                        <li><a href="{{ url('sale/report') }}">{{ Lang::get('views.transaction') }}</a></li>
                                        <li><a href="{{ url('order/track-time') }}">{{ Lang::get('views.time_track') }}</a></li>
                                        <li><a href="{{ url('item/sale') }}">{{ Lang::get('views.item_sale') }}</a></li>
                                        <li><a href="{{ url('category/sale') }}">{{ Lang::get('views.category_sale') }}</a></li>
                                        <li><a href="{{ url('nfc_band/report') }}">{{ Lang::get('views.nfc_band_report') }}</a></li>
                                        <li><a href="{{ url('customer/wallet_history') }}">{{ Lang::get('views.customer_wallet_report') }}</a></li>
                                    </ul>
                                </li>
                            @else
                                <li><a href="{{ url('/product-price') }}">{{ Lang::get('views.product_price') }}</a></li>
                                <li><a href="{{ url('/special-product') }}">{{ Lang::get('views.special-product') }}</a></li>
                                @if ( accountant() or  storeManager())
                                    <li><a href="{{ url('manage-tables') }}">{{ Lang::get('views.manage-tables') }}</a></li>
                                @endif

                            @if (storeManager())
                                   <li><a href="{{ url('/taxes') }}">{{ Lang::get('views.taxes') }}</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                            Reports <span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ url('total/sale') }}">{{ Lang::get('views.sale') }}</a></li>
                                            {{--<li><a href="{{ url('transaction') }}">{{ Lang::get('views.transaction') }}</a></li>--}}
                                            <li><a href="{{ url('sale/report') }}">{{ Lang::get('views.transaction') }}</a></li>
                                            <li><a href="{{ url('order/track-time') }}">{{ Lang::get('views.time_track') }}</a></li>
                                            <li><a href="{{ url('item/sale') }}">{{ Lang::get('views.item_sale') }}</a></li>
                                            <li><a href="{{ url('category/sale') }}">{{ Lang::get('views.category_sale') }}</a></li>
                                        </ul>
                                    </li>
                                @endif
                            @endif
                                <li><a href="{{ url('/menu') }}">{{ Lang::get('views.menu') }}</a></li>
                                <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ ucfirst(Auth::user()->name) }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/change-password') }}">{{ Lang::get('views.change_password') }}</a></li>
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            @include('layouts.notifications')
        </div>
        @yield('content')
        <div id="footer" class="navbar navbar-default navbar-fixed-bottom">
            <div class="container text-center paddingTop10">
                Powered by
                <a href="http://surmountsoft.com" class="Footer-link" target="_blank">Surmount Softech Solutions Pvt. Ltd</a>. &copy; <?php echo (date('Y')) ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/default.js"></script>
    <script src="/js/custom.js"></script>
    <script src="/js/multiple-select.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/jquery-ui-timepicker.js"></script>
    <script src="/js/highcharts.js"></script>
    <script type="text/javascript" src="{{asset('vendor/jsvalidation/js/jsvalidation.min.js')}}"></script>
    @include('layouts.scripts.itemsales')
    @yield('scripts')
</body>
</html>
