<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Specific -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>Geniota Exchange</title>

    <!-- Fav Icon -->
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-16x16.png" sizes="16x16" />

    <!-- Bootstrap CSS -->
    <link href="{{ secure_url('/public') }}/css/bootstrap.min.css" rel="stylesheet">

    <!-- Magnific popup CSS -->
    <!-- <link href="{{ secure_url('/public') }}/css/magnific-popup.css" rel="stylesheet"> -->

    <!-- Magnific popup CSS -->
    <link href="{{ secure_url('/public') }}/css/animate.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <!-- OwlCarousel CSS -->
    <link href="{{ secure_url('/public') }}/css/owl.carousel.min.css" rel="stylesheet">

    <!-- Material icon CSS -->
    <link href="{{ secure_url('/public') }}/css/material-design-iconic-font.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ secure_url('/public') }}/css/style.css" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="{{ secure_url('/public') }}/css/responsive.css" rel="stylesheet">


</head>

<body>
	<div id="app">
    	<!--   Menu area-->
		<div class="menu-area">
		    <div class="container">
		        <nav class="navbar navbar-default">
		            <div class="navbar-header">
		                <a href="index.html" class="navbar-brand"><img src="{{ secure_url('/public') }}/img/logo.png" alt="logo-image"></a>
		                <button type="button" class="collapsed navbar-toggle" data-target="#collapse_menu" data-toggle="collapse">
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                </button>
		            </div>
		            <div class="collapse navbar-collapse" id="collapse_menu">
		                <ul class="nav navbar-nav navbar-right">
		                    <li><a href="{{ secure_url('/markets') }}">Markets</a></li>
		                    <li class="signup"><a href="{{ secure_url('/user/signup') }}" >sign up</a></li>
		                    <li class="login"><a href="{{ secure_url('/user/login') }}">login</a></li>
		                </ul>
		            </div>
		        </nav>
		    </div>
		</div>
		<div class="welcome-slider-area">
		    <div class="backgound-head">
		    	<div class="single-service-slide">
			    	<div class="service-slide-text-table">
			            <div class="service-slide-text-cell">
							<div class="container">
						        <div class="row">
						        	<div style="text-align: center;">
						        		<p data-animation-in="fadeInLeft" data-animation-out="animate-out fadeInLeft">Welcome to Geniota Exchange</p>
                                        <a data-animation-in="fadeInUp" data-animation-out="animate-out fadeInUp" href="{{ secure_url('/') }}/markets" class="btn" style="margin-left: 0px;border:none;background-color: #97C248;">VIEW MARKET</a>
                                    </div>
						            <!-- <div class="col-md-12 text-center">
						                <p data-animation-in="fadeInLeft" data-animation-out="animate-out fadeInLeft">Join the token sale</p>
						                <div id="clockdiv">
	                                      <div>
	                                        <span class="days"></span>
	                                        <div class="smalltext">Days</div>
	                                      </div>
	                                      <div>
	                                        <span class="hours"></span>
	                                        <div class="smalltext">Hours</div>
	                                      </div>
	                                      <div>
	                                        <span class="minutes"></span>
	                                        <div class="smalltext">Minutes</div>
	                                      </div>
	                                      <div>
	                                        <span class="seconds"></span>
	                                        <div class="smalltext">Seconds</div>
	                                      </div>
	                                    </div>
	                                    <div>
	                                        <a data-animation-in="fadeInUp" data-animation-out="animate-out fadeInUp" href="#" class="btn" style="margin-left: 0px;border:none;background-color: #97C248;">BUY NOW</a>
	                                    </div>
						            </div> -->
						        </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!---Three Block Area start-->
		<div class="block-area section-padding">
		    <div class="container">
		        <div class="row">
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-one.png" alt="security-icon">
		                    <h2>Security</h2>
		                    <p>Security is our priority. We employ an agressive cold storage policy on all currencies in our system.</p>
		                </div>
		            </div>
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-two.png" alt="security-icon">
		                    <h2>Currencies</h2>
		                    <p>We aim to support a large number of crypto currencies, and provide a stable market for smaller niche currencies.</p>
		                </div>
		            </div>
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-three.png" alt="security-icon">
		                    <h2>Asset Trading</h2>
		                    <p>We will be providing some unique trading opportunities apart from currencies, more infomation coming soon.</p>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<!---Three Block Area end-->

		<!---Investment Area start-->
		<div class="investment-are section-padding">
		    <div class="container">
		        <div class="row">
		            <div class="col-lg-4 col-md-5">
		                <div class="invest-left-text">
		                    <h1>Always Updated</h1>
		                    <p>We are always updating coin with high liquidity in the market.</p>
		                    <a class="btn btn-start" href="{{ secure_url('/') }}/markets">start trading now</a>
		                </div>
		            </div>
		            <div class="col-md-7 col-lg-7 col-lg-offset-1">
		                <div class="invest-graph">
		                    <img src="{{ secure_url('/public') }}/img/investment/grap.png" alt="graph-image">
		                </div>
		            </div>
		        </div>
		    </div>
		</div>

		<!---Investment Area end-->

		<!---Three Block Area start-->
		<div class="block-area block-two section-padding">
		    <div class="container">
		        <div class="row">
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-four.png" alt="security-icon">
		                    <h2>Fees</h2>
		                    <p>Pay only 0.1% on every market trade.</p>
		                </div>
		            </div>
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-five.png" alt="security-icon">
		                    <h2>Support</h2>
		                    <p>By your side 24 hours a day, our support team will assist you with any issue or question you may have.</p>
		                </div>
		            </div>
		            <div class="col-md-4 text-center">
		                <div class="single-block">
		                    <img src="{{ secure_url('/public') }}/img/icon/icon-six.png" alt="security-icon">
		                    <h2>API</h2>
		                    <p>Trade using our industry leading REST-API or connect via our FIX interface and get access to even more features!</p>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>

		<!---Three Block Area end-->


		<!---CTA Area start-->
		<div class="cta-area section-padding no-padding-top">
		    <div class="container">
		        <div class="row">
		            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
		                <div class="cta-inner">
		                    <h1>You can trade anywhere and anytime</h1>
		                    <form class="login-form-homepage">
		                        <div class="slider-form">
		                            <input type="email" name="username" id="email" placeholder="Email or Username">
		                            <input type="password" name="password" id="password" placeholder="Password">
		                        </div>
		                        <button type="submit" class="btn btn-cta">Login</button>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<!---CtA Area end-->



		<!-- Footer area start-->
		<div class="footer-area section-padding">
		    <div class="container">
		        <div class="row">
		            <div class="footer-inner">
		                <div class="col-md-3 col-sm-12">
		                    <div class="footer-one">
		                        <a href="index.html"><img src="{{ secure_url('/public') }}/img/footer-logo.png" alt="footer-logo"></a>
		                        <ul class="social-links">
		                            <li><a href=""><i class="zmdi zmdi-facebook"></i></a></li>
		                            <li><a href=""><i class="zmdi zmdi-twitter"></i></a></li>
		                            <li><a href=""><i class="zmdi zmdi-linkedin-box"></i></a></li>
		                            <li><a href=""><i class="zmdi zmdi-skype"></i></a></li>
		                            <li><a href=""><i class="zmdi zmdi-youtube"></i></a></li>
		                        </ul>
		                    </div>
		                </div>
		                <div class="col-md-3 col-sm-2">
		                    <div class="footer-two">
		                        <h2>About</h2>
		                        <ul class="social-links">
		                            <li><a href="">About Us</a></li>
		                            <li><a href="">News</a></li>
		                            <li><a href="">Fees &amp; limite</a></li>
		                            <li><a href="">Contact us : admin@geniota.com</a></li>
		                        </ul>
		                    </div>
		                </div>
		                <div class="col-md-3 col-sm-4">
		                    <div class="footer-two">
		                        <h2>Data for business</h2>
		                        <ul class="social-links">
		                            <li><a href="">Market Overview</a></li>
		                            <li><a href="">API</a></li>
		                            <li><a href="">Coin Listing</a></li>
		                            <li><a href="">ICO</a></li>
		                            <li><a href="">Add Token</a></li>
		                        </ul>
		                    </div>
		                </div>
		                <div class="col-md-3 col-sm-3">
		                    <div class="footer-two">
		                        <h2>For Users</h2>
		                        <ul class="social-links">
		                            <li><a href="">support Center</a></li>
		                            <li><a href="">Fees and Limits</a></li>
		                        </ul>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<div class="footer-copyright">
		    <div class="container">
		        <div class="row">
		            <div class="col-md-12 text-center">
		                <p>Copyright &copy; Currency 2018. All rights reserved</p>
		            </div>
		        </div>
		    </div>
		</div>
		<!-- Footer area end-->
		<checkRobotModal token="{{ csrf_token() }}" username="" password=""></checkRobotModal>
	</div>
    <!--Preloader area start-->
    <!-- <div class="preloader-wrapper">
        <div class="preloader">
            <img src="{{ secure_url('/public') }}/img/logo.png" alt="">
            <h2>Loading...</h2>
        </div>
    </div> -->
    <!--Preloader area end-->

    <!--Scroll top are start-->
    <a href="" id="scroll-top"><i class="zmdi zmdi-long-arrow-up"></i></a>
    <!--Scroll top are end-->

    <!-- jQuery (Necessary for all JavaScript plugins) -->
    <script src="{{ secure_url('/public') }}/js/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="{{ secure_url('/public') }}/js/bootstrap.min.js"></script>

    <!-- Main JS -->
    <script src="{{ secure_url('/public') }}/js/main.js"></script>

    <!-- Google Recaptcha -->
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <!-- Vue -->
    <script src="{{secure_url('/public')}}/js/app.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js"></script>
    <script>
        function getTimeRemaining(endtime) {
          var t = Date.parse(endtime) - Date.parse(new Date());
          var seconds = Math.floor((t / 1000) % 60);
          var minutes = Math.floor((t / 1000 / 60) % 60);
          var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
          var days = Math.floor(t / (1000 * 60 * 60 * 24));
          return {
            'total': t,
            'days': days,
            'hours': hours,
            'minutes': minutes,
            'seconds': seconds
          };
        }

        function initializeClock(id, endtime) {
          var clock = document.getElementById(id);
          var daysSpan = clock.querySelector('.days');
          var hoursSpan = clock.querySelector('.hours');
          var minutesSpan = clock.querySelector('.minutes');
          var secondsSpan = clock.querySelector('.seconds');

          function updateClock() {
            var t = getTimeRemaining(endtime);

            daysSpan.innerHTML = t.days;
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

            if (t.total <= 0) {
              clearInterval(timeinterval);
            }
          }

          updateClock();
          var timeinterval = setInterval(updateClock, 1000);
        }

        var deadline = new Date("2018/03/14 22:07:00");
        initializeClock('clockdiv', deadline);
    </script>

    <!-- Histats.com  (div with counter) --><div id="histats_counter"></div>
	<!-- Histats.com  START  (aync)-->
	<script type="text/javascript">var _Hasync= _Hasync|| [];
	_Hasync.push(['Histats.start', '1,2750673,4,601,110,30,00011111']);
	_Hasync.push(['Histats.fasi', '1']);
	_Hasync.push(['Histats.track_hits', '']);
	(function() {
	var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
	hs.src = ('//s10.histats.com/js15_as.js');
	(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
	})();</script>
	<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?2750673&101" alt="" border="0"></a></noscript>
	<!-- Histats.com  END  -->

	<!-- Start Alexa Certify Javascript -->
	<script type="text/javascript">
	_atrk_opts = { atrk_acct:"5xIbq1KAfD20Cs", domain:"geniota.com",dynamic: true};
	(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://certify-js.alexametrics.com/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
	</script>
	<noscript><img src="https://certify.alexametrics.com/atrk.gif?account=5xIbq1KAfD20Cs" style="display:none" height="1" width="1" alt="" /></noscript>
	<!-- End Alexa Certify Javascript -->  

    @include('layouts.alert');
</body>

</html>
