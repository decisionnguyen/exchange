@include('layouts.user.header')
	<div class="bg-signup">
		<div class="container">
			<center >
				<img class="img-custom" src="{{ secure_url('/public') }}/img/logo-signup.png">
				<div class="box-login">
					<p class="font-signup">Login to your account</p>
					<form action="/user/login" method="POST">
                        {{ csrf_field() }}
                        @if($data['callback'] != null)
                        <input type="hidden" name="callback" value="{{ $data['callback'] }}">
                        @endif
						<input class="input-signup" type="text" name="username" placeholder="Username or Email">
						<input class="input-signup" type="password" name="password" placeholder="Password">
                        <div class="g-recaptcha" data-sitekey="6LfF-kgUAAAAAN7MLzhuh4ExArx0p5pJ9y7UiBYi"></div>
						<input class="btn-signup" type="submit" value="LOGIN" style="margin-top: 10px;">
					</form>
                    <a href="/user/signup" class="refo">Register</a>
                    <a href="/user/forgotPassword" class="refo-2">Forgot Password</a>
                    <div style="clear: both"></div>
				</div>
			</center>
				
			</div>
		</div>
	</div>
@include('layouts.user.footer')
@include('layouts.alert')