@include('layouts.header')
<body>
    @include('layouts.navbar')
    <div id="app">
    	<?php Session::has('user') ? $isLogin = 1 : $isLogin = 0 ?>
    	<?php Session::has('user') ? $user_id = Session::get('user')->id : $user_id = 'null' ?>
    	<?php Session::has('user') ? $auth = Session::get('user')->auth : $auth = 'null' ?>
        <trade :is_login="{{ $isLogin }}" :auth="'{{ $auth }}'" :user_id="{{ $user_id }}" :market_id="{{ $data['id'] }}"></trade>
    </div>
    <script type="text/javascript" src="{{ secure_url('/public') }}/charting_library/charting_library.min.js"></script>
    <script type="text/javascript" src="{{ secure_url('/public') }}/datafeeds/udf/dist/polyfills.js"></script>
    <script type="text/javascript" src="{{ secure_url('/public') }}/datafeeds/udf/dist/bundle.js"></script>

    @include('layouts.footer')
    <script type="text/javascript" src="{{ secure_url('/public') }}/plugins/jquery-countdown/jquery.countdown.min.js"></script>
    @include("layouts.alert")
</body>

</html>
