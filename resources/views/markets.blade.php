@include('layouts.header')
<body>
    @include('layouts.navbar')
    <div id="app">
    	<div class="currency-table-area wallet">
	        <div class="container">
	            <div class="row">
	                <div class="col-md-12">
	        			<markets :show_col="['market', 'coinName', 'lastPrice', 'change', '24hvol', '24hhigh', '24hlow']" :is_children="false"></markets>
			       	</div>
	            </div>
	        </div>
	    </div> 		
    </div>
    
    @include('layouts.footer')
    @include("layouts.alert")
    
</body>

</html>
