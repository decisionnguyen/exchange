@include('layouts.header')
<body>
@include('layouts.navbar')
<div id="app">
    <changepassword csrf_token="{{ csrf_token() }}"></changepassword>
</div>    
@include('layouts.footer')
@include("layouts.alert")
</body>
</html>