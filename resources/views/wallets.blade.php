@include('layouts.header')
<link rel="stylesheet" type="text/css" href="{{ secure_url('/public') }}/plugins/intl-tel-input/css/intlTelInput.css">
<body>
    @include('layouts.navbar')
    <div id="app">
        <wallets></wallets>
    </div>
    @include('layouts.footer')
    @include("layouts.alert")
</body>

</html>