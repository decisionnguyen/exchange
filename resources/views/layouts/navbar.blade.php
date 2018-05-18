<div class="header-top-area wallet">
    <div class="container">
        <div class="row">
            <div class="logo">
                <a style="float: left;margin-top: 5px" href="/markets"><img src="{{ secure_url('/public') }}/img/logo.png" alt=""></a>
                <ul style="margin-left: 50px;" class="nav navbar-nav">
                    <li class="{{ $data['title'] == "All Markets" ? "active" : "" }}"><a style="line-height: 30px;color: white;font-size: 12px;font-weight: 700;letter-spacing: 1px;" href="/markets">MARKET</a></li>
                    <li class="{{ $data['title'] == "Wallets" ? "active" : "" }}"><a style="line-height: 30px;color: white;font-size: 12px;font-weight: 700;letter-spacing: 1px;" href="/wallets">WALLET</a></li>
                    <li class="{{ $data['title'] == "Account" ? "active" : "" }}"><a style="line-height: 30px;color: white;font-size: 12px;font-weight: 700;letter-spacing: 1px;" href="/account">ACCOUNT</a></li>
                </ul>
            </div>
            <div class="text-right">
                <div class="row">
                @if(Session::has('user'))
                {{-- <div class="col-md-6 col-md-offset-4 col-sm-5">
                    <div class="tranding-blance">
                        <h5>Estimated Value :</h5>
                        <div class="currency">
                            <p>BTC<span> 0</span> </p>
                            <p>USDT<span> 0</span> </p>
                        </div>
                    </div>
                </div> --}}
                    <ul class="navbar-right navbar-mainpage">
                        <li>
                            <p class="icon"><a style="color:white;" href="/account"><i class="zmdi zmdi-settings custom-font"></i></a></p>
                        </li>
                        <li>
                            <p class="icon"><a style="color:white;" href="/user/logout"><i class="zmdi zmdi-long-arrow-tab custom-font"></i></a></p>
                        </li>
                    </ul>
                    
                @else
                    <ul class="navbar-right navbar-mainpage">
                        <li class="font-row-signup">
                            <i class="zmdi zmdi-accounts-add"></i>
                            <a href="/user/signup">Sign Up</a>
                        </li>
                        <li class="font-row-signup">
                            <i class="zmdi zmdi-accounts-alt"></i>
                            <a href="/user/login">Login</a>
                        </li>
                    </ul>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--   Menu area-->
{{-- div class="menu-area tranding">
    <div class="container">
        <nav class="navbar navbar-default navbar-custom">
            <div class="navbar-header">
                <button type="button" class="collapsed navbar-toggle" data-target="#collapse_menu" data-toggle="collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="collapse_menu">
                <ul class="nav navbar-nav">
                    <li class="{{ $data['title'] == "All Markets" ? "active" : "" }}"><a href="/markets"><p class="custom-font-2">Markets</p></a></li>
                    <li class="{{ $data['title'] == "Wallets" ? "active" : "" }}"><a href="/wallets"><p class="custom-font-2">Wallets</p></a></li>
                    <li class="{{ $data['title'] == "Account" ? "active" : "" }}"><a href="/account"><p class="custom-font-2">Account</p></a></li>
                </ul>
            </div>
        </nav>
    </div>
</div> --}}
<!--    Menu area end    -->