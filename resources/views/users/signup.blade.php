@include('layouts.user.header')
<div class="bg-signup">
	<div class="container">
		<center >
			<img class="img-custom" src="{{ secure_url('/public') }}/img/logo-signup.png">
			<div class="box-signup">
				<p class="font-signup">Create a new account</p>
				<form action="/user/signup" method="POST">
					{{ csrf_field() }}
					<input class="input-signup" type="hidden" name="referral_username" value="{{ $data['username'] }}">
					<input class="input-signup" type="text" name="username" placeholder="Username">
					<input class="input-signup" type="email" name="email" placeholder="Email">
					<input class="input-signup" type="password" name="password" placeholder="Password">
					<input class="input-signup" type="password" name="retype_password" placeholder="Confirm password">
					<div class="g-recaptcha" data-sitekey="6LfF-kgUAAAAAN7MLzhuh4ExArx0p5pJ9y7UiBYi"></div>
					<input class="btn-signup" type="submit" value="SIGN UP" style="margin-top: 10px;">
				</form>
                <p class="ready">Already Registered?<a href="/user/login" class="refo-3 no-margin-top">Login</a></p>
                <div style="clear:both"></div>
			</div>
		</center>
		</div>
	</div>
</div>
@include('layouts.user.footer')
@include('layouts.alert')