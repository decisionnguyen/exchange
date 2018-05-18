@include('layouts.header')
<body>
@include('layouts.navbar')
<div id="app">
    <kycverification csrf_token="{{ csrf_token() }}"></kycverification>
</div>    
@include('layouts.footer')
@include("layouts.alert")
</body>
</html>