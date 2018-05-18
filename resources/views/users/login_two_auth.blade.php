@include('layouts.user.header')
	<div class="bg-signup">
		<div class="container">
			<center >
				<img class="img-custom" src="/public/img/logo-signup.png">
				<div class="box-login">
					<p class="font-signup">Two-factory Authentication</p>
					<form action="{{ secure_url('/user/loginTwoAuth') }}" method="POST">
						{{ csrf_field() }}
						@if($data['callback'] != null)
                        <input type="hidden" name="callback" value="{{ $data['callback'] }}">
                        @endif
						<input class="input-signup" type="text" name="pin" placeholder="6 digit pin code">
						<input class="btn-signup" type="submit" value="LOGIN">
					</form>
				</div>
			</center>
				
		</div>
	</div>
@include('layouts.user.footer')
@include('layouts.alert')