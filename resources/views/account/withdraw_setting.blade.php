@include('layouts.header')
<body>
@include('layouts.navbar')
<div id="app">
    <withdrawsetting csrf_token="{{ csrf_token() }}"></withdrawsetting>
</div>    
@include('layouts.footer')
@include("layouts.alert")
</body>
</html>