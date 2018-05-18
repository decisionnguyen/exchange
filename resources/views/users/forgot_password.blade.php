@include('layouts.user.header')
	<div class="bg-signup">
		<div class="container">
			<center >
				<img class="img-custom" src="{{ secure_url('/public') }}/img/logo-signup.png">
				<div class="box-forgot">
					<p class="font-signup">Forgot your password</p>
					<form action="/user/forgotPassword" method="POST">
						{{ csrf_field() }}
						<input class="input-signup" type="text" name="username" placeholder="Username OR Email">
						<div class="g-recaptcha" data-sitekey="6LfF-kgUAAAAAN7MLzhuh4ExArx0p5pJ9y7UiBYi"></div>
						<input class="btn-signup" type="submit" value="RESET MY PASSWORD" style="margin-top: 10px;">
					</form>
                    <a href="/user/signup" class="refo">Register</a>
                    <a href="/user/login" class="refo-2">Login</a>
                    <div style="clear:both"></div>
				</div>
			</center>
				
			</div>
		</div>
	</div>
@include('layouts.user.footer')
@include('layouts.alert')