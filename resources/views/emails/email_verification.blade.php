<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<style>
		/* latin-ext */
		@font-face {
		  font-family: 'Raleway';
		  font-style: normal;
		  font-weight: 400;
		  src: local('Raleway'), local('Raleway-Regular'), url(https://fonts.gstatic.com/s/raleway/v12/1Ptug8zYS_SKggPNyCMIT4ttDfCmxA.woff2) format('woff2');
		  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
		}
		/* latin */
		@font-face {
		  font-family: 'Raleway';
		  font-style: normal;
		  font-weight: 400;
		  src: local('Raleway'), local('Raleway-Regular'), url(https://fonts.gstatic.com/s/raleway/v12/1Ptug8zYS_SKggPNyC0IT4ttDfA.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
		}
		/* latin-ext */
		@font-face {
		  font-family: 'Raleway';
		  font-style: normal;
		  font-weight: 600;
		  src: local('Raleway SemiBold'), local('Raleway-SemiBold'), url(https://fonts.gstatic.com/s/raleway/v12/1Ptrg8zYS_SKggPNwPIsWqhPANqczVsq4A.woff2) format('woff2');
		  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
		}
		/* latin */
		@font-face {
		  font-family: 'Raleway';
		  font-style: normal;
		  font-weight: 600;
		  src: local('Raleway SemiBold'), local('Raleway-SemiBold'), url(https://fonts.gstatic.com/s/raleway/v12/1Ptrg8zYS_SKggPNwPIsWqZPANqczVs.woff2) format('woff2');
		  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
		}

		* {
		    margin: 0px;
		    padding: 0px;
		}

		.img-logo {
			margin-top: 50px;
		}

		.line {
			width: 100%;
			height: 1px;
			background: #C8C8C8;
			margin-bottom: 20px;
			margin-top: 20px;
		}
		.blue {
			font-size: 16px;
			font-weight: bold;
			color: #96C247;
		}
		.text-content {
			font-size: 14px;
		}
		.btn-submit {
			display: block;
			width: 400px;
			height: 40px;
			background: #96C247;
			color: white;
			border:none;
			font-size: 16px;
			font-weight: bold;
			margin-bottom: 30px;
			line-height: 40px;
		}
		.text-foot {
			font-size: 14px;
			font-weight: bold;
			margin: 0px;
			margin-bottom: 30px;
		}
	</style>
</head>
<body style="font-family: 'Raleway', sans-serif;font-size: 14px;
		    font-weight: 400;
		    color: #333333;
		    line-height: 30px;
		    overflow-x: hidden">
	<div class="container" style="width: 800px;margin: 0 auto;text-align: center;">
		<img src="{{ secure_url('/public/email-assets') }}/img/logo.png" class="img-logo">
		<div class="line"></div>
		<p style="text-align: center;">Your security code is: </p>
		<p class="blue" style="margin-top: 10px;border: 1px solid #96C247;display:inline-block;padding-left: 10px;text-align:center;font: 5ch droid sans mono, consolas, monospace;letter-spacing: 0.5ch;"><b>{{ $pin }}</b></p>
		<div class="line"></div>
		<center><p class="text-foot">This email was sent to no-reply@geniota.com of Geniota.com</br>Please do not reply to this email</p></center>
	</div>
</body>
</html>