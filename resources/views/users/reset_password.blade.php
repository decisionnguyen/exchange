@include('layouts.user.header')
	<div class="bg-signup">
		<div class="container">
			<center >
				<img class="img-custom" src="{{ secure_url('/public') }}/img/logo-signup.png">
				<div class="box-forgot">
					<p class="font-signup">Reset your password</p>
					<form action="/user/resetPassword" method="POST">
						{{ csrf_field() }}
						<input type="hidden" name="hash" value="{{$data['hash']}}">
						<input class="input-signup" type="password" name="password" placeholder="New password">
						<input class="input-signup" type="password" name="retype_password" placeholder="Retype New password">
						<input class="btn-signup" type="submit" value="UPDATE NEW PASSWORD" style="margin-top: 10px;">
					</form>
					<a href="{{ secure_url('/') }}/user/signup" class="refo">Register</a>
                    <a href="{{ secure_url('/') }}/user/login" class="refo-2">Login</a>
                    <div style="clear:both"></div>
				</div>
			</center>
				
			</div>
		</div>
	</div>
@include('layouts.user.footer')
@include('layouts.alert')