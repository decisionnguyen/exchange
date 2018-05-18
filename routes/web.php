<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Orders;
use GuzzleHttp\Client;

Route::get('/', "HomeController@index");

Route::get('/markets', "HomeController@markets");

Route::get('/trade/{market_id}', 'HomeController@trade');

Route::get('/wallets', "HomeController@wallets");

Route::get('/user/login/{callback?}', 'UserController@login');
Route::post('/user/login', 'UserController@postLogin');

Route::get('/user/loginTwoAuth/{callback?}', 'UserController@loginTwoAuth');
Route::post('/user/loginTwoAuth', 'UserController@postLoginTwoAuth');

Route::get('/user/signup/{username?}', 'UserController@signup');
Route::post('/user/signup', 'UserController@postSignup');

Route::get('/user/logout', 'UserController@logout');

Route::get('/user/emailConfirm/{hash}', 'UserController@emailConfirm');
Route::post('/user/resendEmailConfirm', 'UserController@resendEmailConfirm');

Route::get('/user/forgotPassword', 'UserController@forgotPassword');
Route::post('/user/forgotPassword', 'UserController@postForgotPassword');

Route::get('/user/resetPassword/{hash}', 'UserController@resetPassword');
Route::post('/user/resetPassword', 'UserController@postResetPassword');
Route::post('/user/changePassword', 'UserController@postChangePassword');

Route::get('/api/getUserInfo', 'ApiController@getUserInfo');

Route::get('/api/getReferralHistories', 'ApiController@getReferralHistories');

Route::get('/api/getTwoAuth', 'ApiController@getTwoAuth');

Route::get('/api/enabledTwoAuth/{pin?}', 'ApiController@enabledTwoAuth');

Route::get('/api/disabledTwoAuth/{pin?}', 'ApiController@disabledTwoAuth');

Route::get('/api/searchMarkets/{query}', 'ApiController@searchMarkets');

Route::get('/api/getAllMarkets', 'ApiController@getAllMarkets');
Route::get('/api/getMarketInfo/{id}', 'ApiController@getMarketInfo');

Route::post('/api/getBalance/{market_id}', 'ApiController@getBalance');

Route::post('/api/makeSellOrder', 'ApiController@makeSellOrder');
Route::post('/api/makeBuyOrder', 'ApiController@makeBuyOrder');

Route::get('/api/getMarketSellOrder/{id}', 'ApiController@getMarketSellOrder');
Route::get('/api/getMarketBuyOrder/{id}', 'ApiController@getMarketBuyOrder');

Route::get('/api/getMyOrder/{market_id}', 'ApiController@getMyOrder');

Route::get('/api/getMarketTradeHistories/{id}', 'ApiController@getMarketTradeHistories');
Route::get('/api/getWallet', 'ApiController@getWallet');

Route::get('/api/test', 'ApiController@test');

Route::get('/api/cancelOrder/{id}', 'ApiController@cancelOrder');
Route::get('/api/cancelStopLimitOrder/{id}', 'ApiController@cancelStopLimitOrder');

Route::get('/api/createNewAddress/{coin_id}', 'ApiController@createNewAddress');
Route::get('/api/getWalletAddress/{coin_id}', 'ApiController@getWalletAddress');

Route::get('/api/setSessionUserTwoAuth/{flag}', 'ApiController@setSessionUserTwoAuth');
Route::get('/api/getSmsVerification', 'ApiController@getSmsVerification');
Route::post('/api/sendSmsVerification', 'ApiController@sendSmsVerification');
Route::post('/api/enabledSmsVerification', 'ApiController@enabledSmsVerification');
Route::post('/api/disabledSmsVerification', 'ApiController@disabledSmsVerification');
Route::get('/api/switchEmailVerification/{pin?}', 'ApiController@switchEmailVerification');
Route::get('/api/sendEmailVerification', 'ApiController@sendEmailVerification');
Route::get('/api/resendEmailConfirm', 'ApiController@resendEmailConfirm');
Route::get('/api/getKycVerification', 'ApiController@getKycVerification');
Route::post('/api/submitKycVerification', 'ApiController@submitKycVerification');
Route::get('/api/getKycImageByName/{fileName}', "ApiController@getKycImageByName");

Route::get('/account', 'UserController@account');
Route::get('/user/changePassword', 'UserController@changePassword');
Route::get('/user/twoAuth', 'UserController@twoAuth');

Route::get('/user/smsVerification', 'UserController@smsVerification');
Route::get('/user/kycVerification', 'UserController@kycVerification');

Route::post('/wallet/postWithdraw/', 'WalletController@postWithdraw');
Route::post('/wallet/cancelWithdraw/', 'WalletController@cancelWithdraw');

Route::get('/datafeed/config', 'DatafeedController@config');

Route::get('seeding', function() {
	DB::table('markets')->insert([
		'coin_id_first' => 20,
		'coin_id_second' => 4,
		'name' => 'TRX/BTC',
		'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
		'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
	]);
});

Route::get('seeding/coins', function() {
	DB::table('coins')->insert([
		'name' => 'Status',
		'type' => 'erc20',
		'symbol' => 'SNT',
		'logo' => 'snt.png',
		'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
		'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
	]);
});

Route::get('/schema', function () {
	// Schema::table("data_markets", function ($table) {
	// 	$table->renameColumn('24h_volume', 'volume');
	// 	$table->renameColumn('24h_high', 'high');
	// 	$table->renameColumn('24h_low', 'low');
	// });

	// Schema::drop('data_markets');
});

Route::get('/test', function() {
    $content = "Your+security+code+on+GENIOTA+is:+123123";
    $phoneNumber = "84968403324";

    $client = new Client();
    $res = $client->get('https://platform.clickatell.com/messages/http/send?apiKey=Gu1ISKzDQjSakZimlFQ1lg==&to='.$phoneNumber.'&content=' . $content);

    if($res->getStatusCode() != 202) {
        $result = [];
        $result['error']['message'] = 'There was an error sending SMS';
        echo json_encode($result);
        return;
    }

    echo "<pre>";
    print_r($res->getBody());
    echo "</pre>";
});

Route::get('viewMarketStatus', function () {
	$path = base_path('nodeapp/market_status');
	$files = File::allFiles($path);
	foreach ($files as $file)
	{
		$filePath = (string)$file;
		$name = File::name($filePath);
		$status = File::get($filePath);
		echo "MARKET ID: " . $name . " - " . $status;
	    echo "<br>";
	}
});

Route::get('/testSocket', function () {
	return view('testSocket');
});