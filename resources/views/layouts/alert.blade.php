<script>
	toastr.options = {
	  	"closeButton": true,
	  	"debug": false,
	  	"newestOnTop": true,
	  	"progressBar": true,
	  	"positionClass": "toast-top-right",
	  	"showDuration": "3000",
	  	"hideDuration": "3000",
	  	"timeOut": "5000",
	  	"extendedTimeOut": "1000",
	  	"showEasing": "swing",
	  	"hideEasing": "linear",
	  	"showMethod": "fadeIn",
	  	"hideMethod": "fadeOut"
	}
</script>
@if (Session::has('error'))
	<script>toastr["error"]("{{ Session::get('error') }}")</script>
@endif
@if (Session::has('success'))
	<script>toastr["success"]("{{ Session::get('success') }}")</script>
@endif

