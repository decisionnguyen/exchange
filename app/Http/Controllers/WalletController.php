<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\WithdrawLimit;
use App\WithdrawFees;
use App\WithdrawPending;
use App\TwoAuth;
use App\Coins;
use App\Wallets;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RobThree\Auth\TwoFactorAuth;
use App\SmsVerification;
use App\EmailVerification;
use App\KycVerification;
use App\WithdrawHistories;
use App\MarketTradeHistories;
use App\Markets;
use App\EmailConfirm;

class WalletController extends Controller
{
	public function postWithdraw (Request $req) {

    	if (!Session::has('user')) {
    	    Session::flash('error', 'You must login to access');
    	    return redirect('user/login/wallet');
        }   

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
        	Session::flash('error', 'Please check Two-factory Authentication');
			return redirect('user/loginTwoAuth/wallet');
        }

        if(empty($req->address) || empty($req->amount)) {
            Session::flash('error', "Please do not leave all fields blank");
            return redirect()->back()->withInput();
        }

        if(!is_numeric($req->amount)) {
            Session::flash('error', "Amount isn't number. Please try again");
            return redirect()->back()->withInput();
        }

        $fee = WithdrawFees::where('coin_id',$req->coin_id)->first()->toArray()['value'];

        $min = WithdrawLimit::where('coin_id',$req->coin_id)->where('type','min')->first();
        $min = !empty($min) ? $min->toArray()['value'] : $fee;

        $max = WithdrawLimit::where('coin_id',$req->coin_id)->where('type','max')->first();
        $max = !empty($max) ? $max->toArray()['value'] : 1000000;

        $symbol = Coins::find($req->coin_id)->symbol;

        if ($req->amount<$min || $req->amount>$max) {
        	Session::flash('error', 'The smallest to withdraw is '.$min.' ' .$symbol. ' and the largest is ' . $max . ' ' . $symbol);
			return redirect()->back()->withInput();
        }

        $wallets = Wallets::where('coin_id',$req->coin_id)->where('user_id',$user->id)->first();

        if(empty($wallets) || $wallets->amount < $req->amount) {
            Session::flash('error', "You do not have enough ".$symbol." to make a withdrawal");
            return redirect()->back()->withInput();
        }

        $total = $req->amount - $fee;

        if($total == 0) {
            Session::flash('error', "Invalid amount");
            return redirect()->back()->withInput();
        }

        $twoAuth = TwoAuth::where('user_id',$user->id)->first();

        if ($twoAuth && $twoAuth->enabled) {
            if(empty($req->twoAuthPin)) {
                Session::flash('error', 'Please do not leave blank Two-factory Authentication fields');
                return redirect()->back()->withInput();
            }

    		$tfa = new TwoFactorAuth('Geniota');
	        $result = $tfa->verifyCode($twoAuth->secret, $req->twoAuthPin);

	        if (!$result) {
	            Session::flash('error', 'Two-factory security code not correct. Please try again');
	            return redirect()->back()->withInput();
	        }
        }

        $smsVerification = SmsVerification::where('user_id', $user->id)->first();

        if(!empty($smsVerification) && $smsVerification->enabled) {
            if($req->smsVerificationPin != $smsVerification->pin) {
                Session::flash('error', 'SMS Verification security code not correct. Please try again');
                return redirect()->back()->withInput();
            }
        }

        $emailVerification = EmailVerification::where('user_id', $user->id)->first();

        if(!empty($emailVerification) && $emailVerification->enabled) {
            if($req->emailVerificationPin != $emailVerification->pin) {
                Session::flash('error', 'Email Verification security code not correct. Please try again');
                return redirect()->back()->withInput();
            }
        }


        // withdraw limit
        $withdrawLimit = 0;

        $data = EmailConfirm::where('user_id',$user->id)->first();

        if (empty ($data) || !$data->confirmed) {
            Session::flash('error', 'You need confirm your email to withdraw');
            return redirect()->back()->withInput();
        }

        $withdrawLimit = 2;

        $data = KycVerification::where('user_id', $user->id)->orderBy('id', 'desc')->first();

        if(!empty($data) && $data->approved == 1) {
            $withdrawLimit = 50;
        }

        // select withdraw history while 24h
        $history = WithdrawHistories::where('user_id', $user->id)->where('created_at', '<=', date ('Y-m-d H:i:s'))->where('created_at', '>=', date ('Y-m-d H:i:s', time() - 86400))->get()->toArray();
        $withdrawInPast = 0;
        if(!empty($history)) {
            foreach($history as $key=>$value) {

                $market_id = Markets::where('coin_id_first', $value['coin_id'])->first()['id'];
                $price = MarketTradeHistories::select("price")->where('market_id', $market_id)->orderBy('id', 'desc')->limit(1)->first();
                if(empty($price)) $price = 0;
                else $price = $price->price;

                $withdrawInPast += $value['amount'] * $price;
            }
        }

        // select withdraw currently
        $market_id = Markets::where('coin_id_first', $req->coin_id)->first()['id'];
        $price = MarketTradeHistories::select("price")->where('market_id', $market_id)->orderBy('id', 'desc')->limit(1)->first();
        if(empty($price)) $price = 0;
        else $price = $price->price;

        $withdrawCurrently = $req->amount * $price;

        if($withdrawInPast + $withdrawCurrently > $withdrawLimit) {
            Session::flash('error', 'Your 24-hour withdrawal limit is '.$withdrawLimit.' BTC. Please check again');
            return redirect()->back()->withInput();
        }

        $data = new WithdrawPending;
        $data->coin_id = $req->coin_id;
        $data->user_id = $user->id;
        $data->address = $req->address;
        $data->amount = $req->amount;
        $data->fee = $fee;
        $data->total = $total;
        $data->save();

        $wallets->amount -= $req->amount;
        $wallets->save();

        $log = ['coin_id' => $req->coin_id,
                'amount' => $req->amount,
                'pin' => $req->pin,
                'address' => $req->address
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('WithDrawPost', $log); 

        Session::flash('success', 'Withdraw order successful');
        return redirect()->back()->withInput();   

	}

    public function cancelWithdraw (Request $req) {

        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login');
        }   

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth');
        }

        $data = WithdrawPending::find($req->id);

        if(empty($data)) {
            Session::flash('error', 'This withdraw order is not exsits');
            return redirect()->back()->withInput();   
        }

        $wallet = Wallets::where('user_id', $user->id)->where('coin_id', $data->coin_id)->first();

        $wallet->amount += $data->amount;
        $wallet->save();

        $data->delete();

        Session::flash('success', 'Cancel successful');
        return redirect()->back()->withInput();  
    }

}