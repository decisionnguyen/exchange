<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use DB;
use Mail;
use QrCode;
use Validator;
use Storage;
use File;
use Response;
use RobThree\Auth\TwoFactorAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Client;
use App\Events\TradeEvent;
use App\Events\PrivateEvent;
use App\TwoAuth;
use App\Markets;
use App\Coins;
use App\UserTradeHisrories;
use App\MarketTradeHistories;
use App\Users;
use App\SellLimit;
use App\BuyLimit;
use App\OrderPendings;
use App\Orders;
use App\OrderHistories;
use App\ExchangeFees;
use App\Wallets;
use App\LastLogin;
use App\Referral;
use App\EmailConfirm;
use App\DepositPending;
use App\DepositHistories;
use App\WithdrawPending;
use App\WithdrawHistories;
use App\WithdrawFees;
use App\SmsVerification;
use App\EmailVerification;
use App\IcoInfomation;
use App\KycVerification;
use App\OrderStopLimit;
use App\DataMarkets;

class ApiController extends Controller
{
    public function getUserInfo () {
         if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $arr = [];

        $arr['user']['email'] = $user->email;
        $arr['user']['username'] = $user->username;

        $data = EmailConfirm::where('user_id',$user->id)->first();

        $arr['email']['confirmed'] = 0;
        if (!empty($data)) {
            $arr['email']['confirmed'] = $data->confirmed;
        }

        $twoAuth = TwoAuth::where('user_id', $user->id)->first();
        $arr['twoAuth'] = false;
        if(!empty($twoAuth)) {
            $arr['twoAuth'] = $twoAuth->enabled;
        } 

        $smsVerification = SmsVerification::where('user_id', $user->id)->first();
        $arr['smsVerification'] = false;
        if(!empty($smsVerification)) {
            $arr['smsVerification'] = $smsVerification->enabled;
        }

        $emailVerification = EmailVerification::where('user_id', $user->id)->first();
        $arr['emailVerification'] = false;
        if(!empty($emailVerification)) {
            $arr['emailVerification'] = $emailVerification->enabled;
        }

        $kycVerification = KycVerification::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        $arr['kycVerification'] = false;
        if(!empty($kycVerification) && $kycVerification->approved == 1) {
            $arr['kycVerification'] = true;
        }


        $lastLogin = LastLogin::where('user_id',$user->id)->limit(7)->get();

        $arr['lastLogin'] = [];
        if (!empty($lastLogin)) {
            $arr['lastLogin'] = $lastLogin->toArray();
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
       
        echo json_encode($result);
        return;
    }

    public function getTwoAuth () {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        $data = TwoAuth::where('user_id',$user->id)->first();            

        if (!empty($data)) {
            $result = [];
            $result['success'] = true;
            if (!$data->enabled) { 
                $result['data']['secret'] = $data->secret;
                $result['data']['qrcode'] = $data->qrcode;
                $result['data']['enabled'] = $data->enabled;
            } else {
                $result['data']['enabled'] = $data->enabled;
            }

            echo json_encode($result);
            return;
        }

        $tfa = new TwoFactorAuth('Geniota');
        $secret = $tfa->createSecret();
        $qrcode = $tfa->getQRCodeImageAsDataUri('Geniota', $secret);

        $data = new TwoAuth;
        $data->user_id = $user->id;
        $data->secret = $secret;
        $data->qrcode = $qrcode;
        $data->enabled = 0;
        $data->save();

        $log = ['secret' => $data->secret,
                'qrcode' => $data->qrcode,
                'enabled' =>$data->enabled
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('GetTwoAuth', $log); 

        $result = [];
        $result['success'] = true;
        $result['data']['secret'] = $secret;
        $result['data']['qrcode'] = $qrcode;
        $result['data']['enabled'] = $data->enabled;

        echo json_encode($result);
        return;

    }

    public function enabledTwoAuth ($pin = null) {

        if($pin == null) {
            $result = [];
            $result['error']['message'] = 'Please not blank fields';
            echo json_encode($result);
            return;
        }

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');

        $data = TwoAuth::where('user_id',$user->id)->first(); 

        if (empty($data)) {
            $result = [];
            $result['error']['message'] = 'You don\'t have permission to access';
            echo json_encode($result);
            return;
        }

        if ($data->enabled) {
            $result = [];
            $result['error']['message'] = 'Your Two-factory Authentication is enabled';
            echo json_encode($result);
            return;
        }

        $tfa = new TwoFactorAuth('Geniota');
        $result = $tfa->verifyCode($data->secret, $pin);

        if (!$result) {
            $result = [];
            $result['error']['message'] = 'PIN 6 digit not correct. Please try again';
            echo json_encode($result);
            return;
        }

        $data->enabled = 1;
        $data->save();

        $log = [
                'pin' => $pin,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('EnabledTwoAuth', $log); 

        $result = [];
        $result['success'] = true;
        echo json_encode($result);
        return;

    }

    public function disabledTwoAuth ($pin = null) {

        if($pin == null) {
            $result = [];
            $result['error']['message'] = 'Please not blank fields';
            echo json_encode($result);
            return;
        }

         if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');

        $data = TwoAuth::where('user_id',$user->id)->first(); 

        if (empty($data)) {
            $result = [];
            $result['error']['message'] = 'You don\'t have permission to access';
            echo json_encode($result);
            return;
        }

        if (!$data->enabled) {
            $result = [];
            $result['error']['message'] = 'Your Two-factory Authentication is disabled';
            echo json_encode($result);
            return;
        }

        $tfa = new TwoFactorAuth('Geniota');
        $result = $tfa->verifyCode($data->secret, $pin);

        if (!$result) {
            $result = [];
            $result['error']['message'] = 'PIN 6 digit not correct. Please try again';
            echo json_encode($result);
            return;
        }

        $data->enabled = 0;
        $data->save();

        $log = [
                'pin' => $pin,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('DisabledTwoAuth', $log); 

        $result = [];
        $result['success'] = true;
        echo json_encode($result);
        return;

    }

    public function searchMarkets ($query) {
        if ($query='') {
            $result = [];
            $result['error']['message'] = 'Please don\'t leave blank fields';
            echo json_encode($result);
            return;
        }

        $data = Markets::where('name', 'like', '%' . $query . '%')->get()->toJson();

        $result = [];
        $result['success'] = true;
        $result['data'] = $data;
        echo json_encode($result);
        return;
    }

    public function getAllMarkets () {
        $data = Markets::all()->toArray();
   
        foreach ($data as $key=>$value) {

            $market_name = Coins::find($value['coin_id_second'])->symbol;

            if(empty($arr[$market_name])) $index = 0;
            else $index = count($arr[$market_name]);

            $arr[$market_name][$index]['id'] = $value['id'];
            $arr[$market_name][$index]['name'] = $value['name'];
            $arr[$market_name][$index]['is_ico'] = $value['is_ico'];
            $arr[$market_name][$index]['coin_name'] = Coins::where('id',$value['coin_id_first'])->first()->toArray()['name'];

            $dataMarket = DataMarkets::where('market_id', $value['id'])->first();

            $arr[$market_name][$index]['lastPrice'] = (float)$dataMarket->last_price;
            $arr[$market_name][$index]['change'] = (float)$dataMarket->change;
            $arr[$market_name][$index]['hoursVol'] = (float)$dataMarket->volume;
            $arr[$market_name][$index]['hrHigh'] = (float)$dataMarket->high;
            $arr[$market_name][$index]['hrLow'] = (float)$dataMarket->low;

            $last24hFormat = date ('Y-m-d H:i:s' ,time() - 86400);
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
        echo json_encode($result);
        return;
       
    }

    public function getMarketInfo ($id) {

        $arr = [];

        $arr['sum_sell_order'] = Orders::where('market_id',$id)->where('type','sell')->sum('amount');
        $arr['sum_buy_order'] = Orders::where('market_id',$id)->where('type','buy')->sum('value');

        $market = Markets::where('id',$id)->first()->toArray();

        $arr['name'] = $market['name'];
        $arr['coinName'] = Coins::find($market['coin_id_first'])->name;
        $arr['is_ico'] = $market['is_ico'];

        if($market['is_ico']) {
            $icoInfomation = IcoInfomation::find($market['id']);
            $arr['ico_infomation'] = !empty($icoInfomation) ? $icoInfomation->toArray() : null;
        }

        $dataMarket = DataMarkets::where('market_id', $id)->first();

        $arr['lastPrice'] = (float)$dataMarket->last_price;
        $arr['hoursVol'] = (float)$dataMarket->volume;
        $arr['hrHigh'] = (float)$dataMarket->high;
        $arr['hrLow'] = (float)$dataMarket->low;
        
        
        $price = MarketTradeHistories::where('market_id',$id)->limit(2)->orderBy('id','DESC')->get()->toArray();

        if (empty ($price)) {
            $arr['oldPrice'] = 0;
        } else {
            $arr['oldPrice'] = !empty($price[1]['price']) ? $price[1]['price'] : $price[0]['price'];
        }

        $arr['coinFirstSymbol'] = Coins::find($market['coin_id_first'])->symbol;
        $arr['coinSecondSymbol'] = Coins::find($market['coin_id_second'])->symbol;

        $arr['sellOrders'] = Orders::selectRaw('price,sum(amount) as amount, sum(value) as value')->where('market_id',$id)->where('type','sell')->groupBy('price')->limit(20)->orderBy('price')->get()->toArray();

        $arr['buyOrders'] = Orders::selectRaw('price,sum(amount) as amount, sum(value) as value')->where('market_id',$id)->where('type','buy')->groupBy('price')->limit(20)->orderBy('price','DESC')->get()->toArray();

        $arr['tradeHistory'] = MarketTradeHistories::where('market_id', $id)->orderBy('id', 'desc')->limit(11)->get()->toArray();

        if(Session::has('user')) {

            $user = Session::get('user');

            $wallets = Wallets::where('coin_id',$market['coin_id_first'])->where('user_id',$user->id)->first();

            if(!empty($wallets)) {
                $arr['balance'][$arr['coinFirstSymbol']] = $wallets->amount;
            } else {
                $arr['balance'][$arr['coinFirstSymbol']] = 0;
            }


            $wallets = Wallets::where('coin_id',$market['coin_id_second'])->where('user_id',$user->id)->first();

            if(!empty($wallets)) {
                $arr['balance'][$arr['coinSecondSymbol']] = $wallets->amount;
            } else {
                $arr['balance'][$arr['coinSecondSymbol']] = 0;
            }
            
            $arr['myOrders'] = Orders::where('user_id', $user->id)->where('market_id', $id)->orderBy('id', 'desc')->limit(100)->get()->toArray();
            $arr['myStopLimitOrders'] = OrderStopLimit::where('user_id', $user->id)->where('market_id', $id)->orderBy('id', 'desc')->limit(100)->get()->toArray();
        }


        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
        echo json_encode($result);
        return;

    }

    public function getBalance ($market_id) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $arr = [];

        $markets = Markets::find($market_id)->first()->toArray();
        
        $arr['sell']['coinName'] = Coins::where('id',$markets['coin_id_first'])->first()->toArray()['name']; 
        $arr['buy']['coinName'] = Coins::where('id',$markets['coin_id_second'])->first()->toArray()['name'];

        $wallets = Wallets::where('coin_id',$markets['coin_id_first'])->where('user_id',$user->id)->first()->toArray();
        $arr['sell']['balance'] = $wallets['amount'];

        $wallets = Wallets::where('coin_id',$markets['coin_id_second'])->where('user_id',$user->id)->first()->toArray();
        $arr['buy']['balance'] = $wallets['amount'];

        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
        echo json_encode($result);
        return;
    }


    public function getMarketSellOrder ($id) {
        $data = [];

        $orders = Orders::select("price",DB::raw('sum(amount)'),DB::raw('sum(value)'))->where('market_id',$id)->where('type','sell')->groupBy('price')->limit(100)->orderBy('price','DESC')->get()->toArray();

        foreach ($orders as $key=>$value) {
            $data[$key]['price'] = $value['price'];
            $data[$key]['amount'] = $value['sum(amount)'];
            $data[$key]['value'] = $value['sum(value)'];
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $data;
        echo json_encode($result);
        return;
    }

    public function getMarketBuyOrder ($id) {
        $data = [];

        $orders = Orders::select("price",DB::raw('sum(amount)'),DB::raw('sum(value)'))->where('market_id',$id)->where('type','buy')->groupBy('price')->limit(100)->orderBy('price','DESC')->get()->toArray();

        foreach ($orders as $key=>$value) {
            $data[$key]['price'] = $value['price'];
            $data[$key]['amount'] = $value['sum(amount)'];
            $data[$key]['value'] = $value['sum(value)'];
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $data;
        echo json_encode($result);
        return;
    }

    public function makeSellOrder (Request $req) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        if ($req->type == 'limit' && $req->price<=0) {
            $result = [];
            $result['error']['message'] = 'Price must be greater than zero';
            echo json_encode($result);
            return;
        }

        $sellLimit = SellLimit::where('market_id',$req->market_id)->count();

        $min = 0.00000001;
        $max = 1000000;

        if($sellLimit == 2) {
            $min = SellLimit::where('market_id',$req->market_id)->where('type','min')->first()->toArray()['value'];
            $max = SellLimit::where('market_id',$req->market_id)->where('type','max')->first()->toArray()['value'];
        }

        $data = Markets::find($req->market_id)->toArray();

        $coinSymbolFirst = Coins::find($data['coin_id_first'])->symbol;

        if ($req->amount < $min || $req->amount > $max) {
            $result = [];
            $result['error']['message'] = 'The smallest to sell is '.number_format($min,8,'.',',').' ' .$coinSymbolFirst. ' and the largest is ' . number_format($max,2,'.',',') . ' ' . $coinSymbolFirst;
            echo json_encode($result);
            return;
        }

        $wallet = Wallets::where('coin_id',$data['coin_id_first'])->where('user_id',$user->id)->first();

        if(empty($wallet)) {
            $result = [];
            $result['error']['message'] = 'You do not have enough '.$req->amount.' ' . $coinSymbolFirst;
            echo json_encode($result);
            return;
        }

        if ($wallet->amount < $req->amount) {
            $result = [];
            $result['error']['message'] = 'You do not have enough '.$req->amount.' ' . $coinSymbolFirst;
            echo json_encode($result);
            return;
        }

        // $pending = new OrderPendings;

        // $pending->order_type = $req->type;
        // $pending->type = 'buy';
        // $pending->market_id = $req->market_id;
        // $pending->user_id = $user->id;
        // $pending->price = $req->price;
        // $pending->amount = $req->amount;
        // $pending->stop = $req->stop;
        // $pending->save();

        $fee = ExchangeFees::where('market_id', $req->market_id)->first();
        if(!empty($fee)) $fee = $fee->value;
        else $fee = 0.25;

        // emit event
        $emitData['hash'] = str_random(64);
        $emitData['type'] = $req->type;
        $emitData['market_id'] = $req->market_id;
        $emitData['user_id'] = $user->id;
        $emitData['price'] = $req->price;
        $emitData['amount'] = $req->amount;
        $emitData['stop'] = $req->stop;
        $emitData['fee'] = $fee;
        $emitData['market'] = Markets::find($req->market_id)->toArray();
        $emitData['coinFirst'] = Coins::find($emitData['market']['coin_id_first'])->toArray();
        $emitData['coinSecond'] = Coins::find($emitData['market']['coin_id_second'])->toArray();
        
        event(new TradeEvent(0,'new-sell-order', json_encode($emitData)));

        $log = ['market_id' => $req->market_id,
                'price' => $req->price,
                'amount' => $req->amount
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('MakeSellOrder', $log);

        $result = [];
        $result['success']['message'] = "Make order successful";
        echo json_encode($result);
        return;
    }

    public function makeBuyOrder (Request $req) {

         if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        if ($req->type == 'limit' && $req->price<=0) {
            $result = [];
            $result['error']['message'] = 'Price must be greater than zero';
            echo json_encode($result);
            return;
        }

        $buyLimit = BuyLimit::where('market_id',$req->market_id)->count();

        $min = 0.00000001;
        $max = 1000000;

        if($buyLimit == 2) {
            $min = BuyLimit::where('market_id',$req->market_id)->where('type','min')->first()->toArray()['value'];
            $max = BuyLimit::where('market_id',$req->market_id)->where('type','max')->first()->toArray()['value'];
        }

        $data = Markets::find($req->market_id)->toArray();
        $coinSymbolFirst = Coins::find($data['coin_id_first'])->symbol;
        $coinSymbolSecond = Coins::find($data['coin_id_second'])->symbol;

        if ($req->amount<$min || $req->amount>$max) {
            $result = [];
            $result['error']['message'] = 'The smallest to buy is '.number_format($min,8,'.',',').' ' .$coinSymbolFirst. ' and the largest is ' . number_format($max,2,'.',',') . ' ' . $coinSymbolFirst;
            echo json_encode($result);
            return;
        }

        $fee = ExchangeFees::where('market_id', $req->market_id)->first();
        if(!empty($fee)) $fee = $fee->value;
        else $fee = 0.25;

        $total = ($req->price * $req->amount) + ($req->price * $req->amount * $fee / 100);

        $wallet = Wallets::where('coin_id',$data['coin_id_second'])->where('user_id',$user->id)->first();

        if(empty($wallet)) {
            $result = [];
            $result['error']['message'] = 'You do not have ' . $coinSymbolSecond . ' wallet. Please create a ' .$coinSymbolSecond. ' wallet';
            echo json_encode($result);
            return;
        }

        if (($req->type == 'limit' || $req->type == 'stop-limit') && $wallet->amount < $total) {
            $result = [];
            $result['error']['message'] = 'You do not have enough '.$total.' ' . $coinSymbolSecond;
            echo json_encode($result);
            return;
        }

        if($req->type == 'stop-limit') {
            $lastPrice = MarketTradeHistories::where('market_id', $req->market_id)->orderBy('id','desc')->first();

            if(!empty($lastPrice)) $lastPrice = $lastPrice->price;

            if($req->price == $lastPrice) {
                $req->type = 'limit';
            }
        }

        // $pending = new OrderPendings;

        // $pending->order_type = $req->type;
        // $pending->type = 'buy';
        // $pending->market_id = $req->market_id;
        // $pending->user_id = $user->id;
        // $pending->price = $req->price;
        // $pending->amount = $req->amount;
        // $pending->stop = $req->stop;
        // $pending->save();

        // emit event
        $emitData['hash'] = str_random(64);
        $emitData['type'] = $req->type;
        $emitData['market_id'] = $req->market_id;
        $emitData['user_id'] = $user->id;
        $emitData['price'] = $req->price;
        $emitData['amount'] = $req->amount;
        $emitData['stop'] = $req->stop;
        $emitData['fee'] = $fee;
        $emitData['market'] = Markets::find($req->market_id)->toArray();
        $emitData['coinFirst'] = Coins::find($emitData['market']['coin_id_first'])->toArray();
        $emitData['coinSecond'] = Coins::find($emitData['market']['coin_id_second'])->toArray();

        event(new TradeEvent(0, 'new-buy-order', json_encode($emitData)));

        $log = ['market_id' => $req->market_id,
                'price' => $req->price,
                'amount' => $req->amount
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('MakeBuyOrder', $log);

        $result = [];
        $result['success']['message'] = "Make order successful";
        echo json_encode($result);
        return;
    }

    public function getMyOrder ($market_id) {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $orders = Orders::where('user_id',$user->id)->where('market_id',$market_id)->orderBy('created_at','DESC')->get()->toArray();

        $result = [];
        $result['success'] = true;
        $result['data'] = $orders;
        echo json_encode($result);
        return;
    }

    public function getMarketTradeHistories ($id) {
        $histories = MarketTradeHistories::where('market_id',$id)->limit(100)->orderBy('created_at','DESC')->get()->toArray();

        $result = [];
        $result['success'] = true;
        $result['data'] = $histories;
        echo json_encode($result);
        return;
    }

    public function getWallet () {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $coins = Coins::all();

        $arr = [];

        foreach($coins as $key=>$value) {
            $arr['coins'][$key] = $value;
            $wallet = Wallets::where('coin_id',$value['id'])->where('user_id', $user->id)->first();
            $arr['coins'][$key]['wallet'] = empty($wallet) ? null : $wallet->toArray(); 

            if($arr['coins'][$key]['wallet'] != null && !empty($wallet->address) && empty($wallet->qrcode) ) {
                // create file png of qrcode
                QrCode::size(500)->generate($wallet->address, public_path('qrcode') . '\\'.$wallet->address.'.svg');
                $wallet->qrcode = $wallet->address . '.svg';
                $wallet->update();
            }

            $fee = WithdrawFees::where('coin_id', $value['id'])->first();

            $arr['coins'][$key]['fee'] = !empty($fee) ? $fee->value : 0;

            // on pending
            $onPending = DepositPending::where('coin_id', $value['id'])->where('user_id', $user->id)->sum('amount');
            if($onPending == null) $onPending = 0;

            $arr['coins'][$key]['onPending'] = $onPending;

            // on order
            // get all market
            $onOrder = 0;

            $allMarket = Markets::where('coin_id_first', $value['id'])->get();
            foreach ($allMarket as $key1 => $value1) {
                $temp = Orders::where('market_id', $value1['id'])->where('user_id', $user->id)->where('type', 'sell')->sum('amount');
                if($temp != null) $onOrder += $temp;
            }

            $allMarket = Markets::where('coin_id_second', $value['id'])->get();
            foreach ($allMarket as $key1 => $value1) {
                $temp = Orders::where('market_id', $value1['id'])->where('user_id', $user->id)->where('type', 'buy')->sum('total');
                if($temp != null) $onOrder += $temp;
            }

            $arr['coins'][$key]['onOrder'] = $onOrder;
        }

        // $arr['coins'] = array_sort_custom($arr['coins'], 'sort', SORT_ASC);

        $arr['deposit']['pending'] = DepositPending::where('user_id',$user->id)->orderBy('id', 'desc')->get()->toArray();

        foreach($arr['deposit']['pending'] as $key=>$value) {
            $arr['deposit']['pending'][$key]['coinSymbol'] = Coins::find($value['coin_id'])->symbol;
        }

        $arr['deposit']['history'] = DepositHistories::where('user_id', $user->id)->orderBy('id', 'desc')->get()->toArray();

        foreach($arr['deposit']['history'] as $key=>$value) {
            $arr['deposit']['history'][$key]['coinSymbol'] = Coins::find($value['coin_id'])->symbol;
        }

        $arr['withdraw']['pending'] = WithdrawPending::where('user_id',$user->id)->orderBy('id', 'desc')->get()->toArray();

        foreach($arr['withdraw']['pending'] as $key=>$value) {
            $arr['withdraw']['pending'][$key]['coinSymbol'] = Coins::find($value['coin_id'])->symbol;
        }

        $arr['withdraw']['history'] = WithdrawHistories::where('user_id', $user->id)->orderBy('id', 'desc')->get()->toArray();

        foreach($arr['withdraw']['history'] as $key=>$value) {
            $arr['withdraw']['history'][$key]['coinSymbol'] = Coins::find($value['coin_id'])->symbol;
        }

        $temp = TwoAuth::where('user_id', $user->id)->first();
        if(empty($temp)) $arr['isEnabledTwoAuth'] = false;
        else $arr['isEnabledTwoAuth'] = $temp->enabled;

        $temp = SmsVerification::where('user_id', $user->id)->first();
        if(empty($temp) || !$temp->enabled) $arr['smsVerification'] = false;
        else {
            $arr['smsVerification']['phoneNumber'] = $temp->phonenumber;
            $arr['smsVerification']['country'] = $temp->country;
        }

        $temp = EmailVerification::where('user_id', $user->id)->first();
        if(empty($temp)) $arr['emailVerification'] = false;
        else $arr['emailVerification'] =  $temp->enabled;

        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
        echo json_encode($result);
        return;
    }

    public function cancelOrder ($id) {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $data =  Orders::where('id',$id)->where('user_id',$user->id)->first();

        if (empty($data)) {
            $result = [];
            $result['error']['message'] = 'This order not exists';
            echo json_encode($result);
            return;
        }
        
        $market = Markets::where('id',$data->market_id)->first()->toArray();

        if ($data->type=='sell') {
            $wallet = Wallets::where('coin_id',$market['coin_id_first'])->where('user_id',$user->id)->first();
            $wallet->amount += $data->amount;
            $wallet->save();
        } else {
            $wallet = Wallets::where('coin_id',$market['coin_id_second'])->where('user_id',$user->id)->first();
            $wallet->amount += $data->total;
            $wallet->save();
        }

        $dataa = new OrderHistories;
        $dataa->market_id = $data->market_id;
        $dataa->user_id = $data->user_id;
        $dataa->type = $data->type;
        $dataa->value = $data->value;
        $dataa->price = $data->price;
        $dataa->total = $data->total;
        $dataa->fee = $data->fee;
        $dataa->amount = $data->amount;
        $dataa->status = 0;
        $dataa->save();

        $data->delete();

        $log = ['id' => $id,
                'type' => $dataa->type,
                'market_id' => $dataa->market_id,
                'amount' => $dataa->amount,
                'price' => $dataa->price,
                'value' => $dataa->value
                ];

        $symbol = Coins::find($wallet->coin_id)->symbol;

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('CancelOrder', $log);

        // emit balance private channel
        // $typeOfCoinId = $data->type == 'buy' ? 'coin_id_second' : 'coin_id_first';
        // $coin_id = $market[$typeOfCoinId];
        // $symbol = Coins::find($coin_id)->symbol;
        // $message = [];
        // $message[$symbol] = $wallet->amount;
        // event(new PrivateEvent($data->user_id, 'change-balance', $message));

        // emit my order private channel
        // event(new PrivateEvent($data->user_id, 'change-my-order', ["data" => Orders::where('market_id',$data->market_id)->where('user_id', $data->user_id)->orderBy('id', 'desc')->limit(100)->get()->toArray(), "market_id" => $data->market_id]));

        // emit buy orders
        // $orderBy = $data->type == 'buy' ? 'desc' : 'asc';
        // $orders = Orders::selectRaw('price,sum(amount) as amount, sum(value) as value')->where('market_id',$data->market_id)->where('type',$data->type)->groupBy('price')->limit(100)->orderBy('price',$orderBy)->get()->toArray();
        // if($data->type == 'buy')
        //     event(new TradeEvent($data->market_id, 'change-buy-order', $orders));
        // else
        //     event(new TradeEvent($data->market_id, 'change-sell-order', $orders));

        // emit sum total
        // if($data->type == 'buy') {
        //     event(new TradeEvent($data->market_id, 'change-sum-buy-order', Orders::where('market_id',$data->market_id)->where('type','buy')->sum('value')));
        // }

        // if($data->type == 'sell') {
        //     event(new TradeEvent($data->market_id, 'change-sum-sell-order', Orders::where('market_id',$data->market_id)->where('type','sell')->sum('amount')));
        // }

        $result = [];
        $result['success']['message'] = "Cancel order successful";
        $result['balance'][$symbol] = $wallet->amount; 
        echo json_encode($result);
        return;
    }

    public function cancelStopLimitOrder ($id) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $data =  OrderStopLimit::where('id',$id)->where('user_id',$user->id)->first();

        if (empty($data)) {
            $result = [];
            $result['error']['message'] = 'This order not exists';
            echo json_encode($result);
            return;
        }
        
        $market = Markets::where('id',$data->market_id)->first()->toArray();

        if ($data->type=='sell') {
            $wallet = Wallets::where('coin_id',$market['coin_id_first'])->where('user_id',$user->id)->first();
            $wallet->amount += $data->amount;
            $wallet->save();
        } else {
            $wallet = Wallets::where('coin_id',$market['coin_id_second'])->where('user_id',$user->id)->first();
            $wallet->amount += $data->total;
            $wallet->save();
        }

        $log = ['id' => $id,
                'type' => $data->type,
                'market_id' => $data->market_id,
                'amount' => $data->amount,
                'price' => $data->price,
                'value' => $data->value
                ];

        $data->delete();

        // emit balance private channel
        $typeOfCoinId = $data->type == 'buy' ? 'coin_id_second' : 'coin_id_first';
        $coin_id = $market[$typeOfCoinId];
        $symbol = Coins::find($coin_id)->symbol;
        $message = [];
        $message[$symbol] = $wallet->amount;
        event(new PrivateEvent($data->user_id, 'change-balance', $message));
        // emit my order private channel
        event(new PrivateEvent($data->user_id, 'change-my-stop-limit-order', ["data" => OrderStopLimit::where('market_id',$data->market_id)->where('user_id', $data->user_id)->orderBy('id', 'desc')->limit(100)->get()->toArray(), "market_id" => $data->market_id]));


        $symbol = Coins::find($wallet->coin_id)->symbol;

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('CancelStopLimitOrder', $log);

        $result = [];
        $result['success']['message'] = "Cancel order successful";
        $result['balance'][$symbol] = $wallet->amount; 
        echo json_encode($result);
        return;
    }

    public function cancelWithdraw ($id) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        } 

        $data =  WithdrawPending::where('id',$id)->where('user_id',$user->id)->first();

        if (empty($data)) {
            $result = [];
            $result['error']['message'] = 'This withdraw order not exists';
            echo json_encode($result);
            return;
        }

        $dataa = new WithdrawHistories;
        $dataa->coin_id = $data->coin_id;
        $dataa->user_id = $data->user_id;
        $dataa->address = $data->address;
        $dataa->amount = $data->amount;
        $dataa->status = 0;
        $dataa->save();

        $data->delete();

        $log = ['coin_id' => $dataa->coin_id,
                'address' => $dataa->address,
                'amount' => $dataa->amount,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('CancelWithdraw', $log); 

        $result = [];
        $result['success'] = true;
        echo json_encode($result);
        return;       

    }

    public function createNewAddress ($coin_id) {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $wallet = Wallets::where('user_id', $user->id)->where('coin_id',$coin_id)->first();

        if(!empty($wallet)) {

            if($wallet->address != null) {
                $result = [];
                $result['error']['message'] = 'You already have a wallet address';
                echo json_encode($result);
                return;
            }

            if($wallet->new_address_pending) {
                $result = [];
                $result['error']['message'] = 'You have a wallet address creation request. Please wait';
                echo json_encode($result);
                return;
            }

            if(!$wallet->new_address_pending) {
                $wallet->new_address_pending = 1;
                $wallet->save();

                $result = [];
                $result['success'] = true;
                echo json_encode($result);
                return;
            }
            
        }

        // check if this coin is erc20
        $address = false;
        if(Coins::find($coin_id)->type == 'erc20') {

            $coins = Coins::where('type','erc20')->get()->toArray();
            foreach ($coins as $value) {
                $data =  Wallets::where('user_id',$user->id)->where('coin_id',$value['id'])->whereNotNull('address')->first();

                if (!empty($data)) {
                    $address = $data->address;
                    break;
                }
            }
        }


        $wallet = new Wallets;
        $wallet->user_id = $user->id;
        $wallet->coin_id = $coin_id;
        $wallet->amount = 0;
        
        if (!$address) {
            $wallet->new_address_pending = 1;
        } else {
            $wallet->address = $address;
            $wallet->new_address_pending = 0;
        }

        $wallet->save();
       

        $result = [];
        $result['success'] = true;
        echo json_encode($result);
        return;
    }

    public function getWalletAddress ($coin_id) {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $wallet = Wallets::where('user_id', $user->id)->where('coin_id',$coin_id)->first();

        if(empty($wallet)) {
            $result = [];
            $result['error']['message'] = 'Please click button create new address';
            echo json_encode($result);
            return;
        }

        if($wallet->address == null) {
            $result = [];
            $result['error']['message'] = 'pending...';
            echo json_encode($result);
            return;
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $wallet->address;
        echo json_encode($result);
        return;
    }

    public function setSessionUserTwoAuth ($flag) {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        if (!$flag) {
            $enabled = TwoAuth::where('user_id',$user->id)->first()->toArray()['enabled'];

            if (!$enabled) {
                $result = [];
                $result['error']['message'] = 'You don\'t have permission to access';
                echo json_encode($result);
                return;
            }
        }

        Session::put('user_two_auth',$flag);

        $result = [];
        $result['success'] = true;
        echo json_encode($result);
        return;
    }

    public function getReferralHistories () {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = [];

        $referral = Referral::where('parent_id',$user->id)->get();
        if (!empty($referral)) {
            $referral = $referral->toArray();
            foreach ($referral as $key=>$value) {
                $dataa = Users::find($value['user_id']);

                $data[$key]['id'] = $dataa->id;
                $data[$key]['username'] = $dataa->username;
                $data[$key]['email'] = $dataa->email;
            }
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $data;
        echo json_encode($result);
        return;
    }

    public function cancelWithdrawOrder () {

    }

    public function sendSmsVerification (Request $req) {

        $phoneNumber = $req->phoneNumber;

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $phoneNumber = trim($phoneNumber);
        if($phoneNumber[0] == '+') $phoneNumber = ltrim($phoneNumber, '+');

        if(!is_numeric($phoneNumber)) {
            $result = [];
            $result['error']['message'] = 'Phone number must be numeric';
            echo json_encode($result);
            return;
        }

        if($req->country == null) {
            $result = [];
            $result['error']['message'] = 'Please select the country where your phone number is to be provided';
            echo json_encode($result);
            return;
        }

        $data = SmsVerification::where('user_id', $user->id)->first();

        if(empty($data)) {
            $data = new SmsVerification;
            $data->user_id = $user->id;
        } else {

            $updated_at = strtotime($data->toArray()['updated_at']);
            $now = time();

            if($now - $updated_at < 60) {
                $result = [];
                $result['error']['message'] = 'You need to wait '.(60 - ($now - $updated_at)).' seconds';
                echo json_encode($result);
                return;
            }

            if($data->enabled == true && $data->phonenumber != $phoneNumber) {
                $result = [];
                $result['error']['message'] = 'Please disabled this function before you want to change the phone number';
                echo json_encode($result);
                return;
            }
        }

        $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9); // random 6 digit
        $data->pin = $pin;
        $data->phonenumber = $phoneNumber;
        $data->country = $req->country;

        $content = "Your+security+code+on+GENIOTA+is:+" . $pin;

        $client = new Client();
        $res = $client->get('https://platform.clickatell.com/messages/http/send?apiKey=Gu1ISKzDQjSakZimlFQ1lg==&to='.$phoneNumber.'&content=' . $content);

        if($res->getStatusCode() != 202) {
            $result = [];
            $result['error']['message'] = 'There was an error sending SMS';
            echo json_encode($result);
            return;
        }

        $json = $res->getBody();
        $arr = json_decode($json);

        if($arr->error != null) {
            $result = [];
            $result['error']['message'] = $arr->errorDescription;
            echo json_encode($result);
            return;
        }

        $data->save();

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Send SMS successful";
        echo json_encode($result);
        return;

    }

    public function getSmsVerification () {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = SmsVerification::where('user_id', $user->id)->first();

        if(empty($data)) $arr['enabled'] = false;
        else {
            $arr['enabled'] = $data->enabled;
            $arr['updated_at'] = $data->toArray()['updated_at'];
            $arr['phoneNumber'] = $data->phonenumber;
            $arr['country'] = $data->country;
        }

        $result = [];
        $result['success'] = true;
        $result['data'] = $arr;
        echo json_encode($result);
        return;
    }

    public function enabledSmsVerification (Request $req) {

        $pin = $req->pin;

        if($pin == null || !is_numeric($pin) || strlen($pin) != 6) {
            $result = [];
            $result['error']['message'] = 'Please correct PIN (6 digit)';
            echo json_encode($result);
            return;
        }

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');

        $data = SmsVerification::where('user_id', $user->id)->first();

        if(empty($data)) {
            $result = [];
            $result['error']['message'] = 'You have not yet sent a message about the phone number. Please press the SEND button';
            echo json_encode($result);
            return;
        } 

        if($data->enabled == true) {
            $result = [];
            $result['error']['message'] = 'Your account has been enabled';
            echo json_encode($result);
            return;
        }

        if($pin != $data->pin) {
            $result = [];
            $result['error']['message'] = 'PIN is incorrect. Please try again';
            echo json_encode($result);
            return;
        }

        $data->pin = 0;
        $data->enabled = true;
        $data->save();

        $log = [
            'pin' => $pin
        ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('EnabledSmsVerification', $log); 

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Enabled SMS Verification successful";

        echo json_encode($result);
        return;

    }

    public function disabledSmsVerification (Request $req) {

        $pin = $req->pin;

        if($pin == null || !is_numeric($pin) || strlen($pin) != 6) {
            $result = [];
            $result['error']['message'] = 'Please correct PIN (6 digit)';
            echo json_encode($result);
            return;
        }

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');

        $data = SmsVerification::where('user_id', $user->id)->first();

        if(empty($data)) {
            $result = [];
            $result['error']['message'] = 'You have not yet sent a message about the phone number. Please press the SEND button';
            echo json_encode($result);
            return;
        } 

        if($data->enabled == false) {
            $result = [];
            $result['error']['message'] = 'Your account has been disabled';
            echo json_encode($result);
            return;
        }

        if($pin != $data->pin) {
            $result = [];
            $result['error']['message'] = 'PIN is incorrect. Please try again';
            echo json_encode($result);
            return;
        }

        $data->pin = 0;
        $data->enabled = false;
        $data->save();

        $log = [
            'pin' => $pin
        ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('DisabledSMSVerification', $log); 

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Enabled SMS Verification successful";

        echo json_encode($result);
        return;
    }

    public function switchEmailVerification ($pin = null) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = EmailVerification::where('user_id', $user->id)->first();

        if(empty($data)) {
            $data = new EmailVerification;
            $data->user_id = $user->id;
            $data->enabled = true;
            $data->pin = 0;
        } else {

            if($data->enabled) {

                if($pin == null || !is_numeric($pin) || strlen($pin) != 6 || $pin != $data->pin) {
                    $result = [];
                    $result['error']['message'] = 'Security code is not correct';
                    echo json_encode($result);
                    return;
                }
                
            }

            $data->pin = 0;
            $data->enabled = !$data->enabled;
        }

        $data->save();

        $result = [];
        $result['success'] = [];
        if($data->enabled == true)
            $result['success']['message'] = "Enabled Email Verification successful";
        else
            $result['success']['message'] = "Disabled Email Verification successful";

        echo json_encode($result);
        return;
    }

    public function sendEmailVerification () {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = EmailVerification::where('user_id', $user->id)->first();

        if(empty($data) || !$data->enabled) {
            $result = [];
            $result['error']['message'] = 'You have not enabled this feature yet';
            echo json_encode($result);
            return;
        }

        $updated_at = strtotime($data->toArray()['updated_at']);
        $now = time();

        if($now - $updated_at < 60) {
            $result = [];
            $result['error']['message'] = 'You need to wait '.(60 - ($now - $updated_at)).' seconds';
            echo json_encode($result);
            return;
        }

        $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9); // random 6 digit

        $data->pin = $pin;
        $data->save();

        // send email confirm
        Mail::send('emails.email_verification', ['pin'=>$pin], function ($message) use ($pin, $user) {
            $message->subject("Security code: ".$pin." | Geniota.com");
            $message->from('no-reply@geniota.com', 'Geniota Exchange Platform');
            $message->to($user->email);
        });

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Send Email Verfication successful";

        echo json_encode($result);
        return;
    }

    public function resendEmailConfirm () {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = EmailConfirm::where('user_id',$user->id)->first();

        if(empty($data)) {
            $data = new EmailConfirm;
            $data->user_id = $user->id;
        } else {
            if($data->confirmed) {
                $result = [];
                $result['error']['message'] = 'You already confirmed your email';
                echo json_encode($result);
                return;
            }

            $updated_at = strtotime($data->toArray()['updated_at']);
            $now = time();

            if($now - $updated_at < 60) {
                $result = [];
                $result['error']['message'] = 'You need to wait '.(60 - ($now - $updated_at)).' seconds';
                echo json_encode($result);
                return;
            }
        }

        $hash = str_random(64);
        $data->hash = $hash;
        $data->save();

        // send email confirm
        Mail::send('emails.email_confirm', ['username'=>$user->username, 'hash'=>$hash], function ($message) use ($hash,$user) {
            $message->subject("Confirm your account | Geniota.com | #" . $hash);
            $message->from('no-reply@geniota.com', 'Geniota Exchange Platform');
            $message->to($user->email);
        });

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Resend email confirm successful";

        echo json_encode($result);
        return;
    }

    public function getKycVerification () {
        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $data = KycVerification::where('user_id', $user->id)->orderBy('id', 'desc')->first();

        if(empty($data)) {
            $result = [];
            $result['isSent'] = false;
            $result['isApproved'] = false;
        }else{
            $result = [];
            $result['isSent'] = true;
            $result['isApproved'] = $data->approved;
            // get history
            if($data->approved == 2) {
                $result['isSent'] = false;
                $result['isApproved'] = false;
                $data = KycVerification::where('user_id', $user->id)->orderBy('id','desc')->first();
                $result['history'] = $data->toArray()['updated_at'];
            }
        }

        echo json_encode($result);
        return;
    }

    public function getKycImageByName ($filename) {

        $path = storage_path('app\\kycImages\\' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function submitKycVerification (Request $req) {

        if (!Session::has('user')) {
            $result = [];
            $result['error']['message'] = 'You must login to access';
            echo json_encode($result);
            return;
        }

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            $result = [];
            $result['error']['message'] = 'Please check Two-factory Authentication';
            echo json_encode($result);
            return;
        }

        $firstName = trim($req->firstName);
        $lastName = trim($req->lastName);
        $number = trim($req->number);
        $country = trim($req->country);
        $type = $req->type;
        $frontImage = $req->frontImage;
        $backImage = $req->backImage;
        $selfieImage = $req->selfieImage;

        if($type != 'id' && $type != 'passport') return;

        if($firstName == "" || $lastName == "" || $number == "" || ($type == 'passport' && $country == "")) {
            $result = [];
            $result['error']['message'] = 'Please do not leave any fields empty';
            echo json_encode($result);
            return;
        }

        if($frontImage == false || $backImage == false || $selfieImage == false) {
            $result = [];
            $result['error']['message'] = 'Please upload the full image file';
            echo json_encode($result);
            return;
        }

        $validate = Validator::make(
            $req->all(),
            [
                'frontImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'backImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'selfieImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]
        );

        if($validate->fails()){
            $result = [];
            $result['uploadError'] = $validate->errors()->toJson();
            echo json_encode($result);
            return;
        }

        $count = KycVerification::where('user_id', $user->id)->where('approved', 0)->count();
        if($count != 0) return;
        $count = KycVerification::where('user_id', $user->id)->where('approved', 1)->count();
        if($count != 0) return;

        // upload file
        $extension = $frontImage->getClientOriginalExtension();
        $frontImageFileName = str_random(32) . "." . $extension;
        Storage::disk('local') -> put("kycImages/" . $frontImageFileName, file_get_contents($frontImage->getRealPath()));
        $extension = $backImage->getClientOriginalExtension();
        $backImageFileName = str_random(32) . "." . $extension;
        Storage::disk('local') -> put("kycImages/" . $backImageFileName, file_get_contents($backImage->getRealPath()));
        $extension = $selfieImage->getClientOriginalExtension();
        $selfieImageFileName = str_random(32) . "." . $extension;
        Storage::disk('local') -> put("kycImages/" . $selfieImageFileName, file_get_contents($selfieImage->getRealPath()));

        // save data
        $data = new KycVerification;
        $data->user_id = $user->id;
        $data->type = $type;
        $data->first_name = $firstName;
        $data->last_name = $lastName;
        $data->country = $country;
        $data->number = $number;
        $data->front_image = $frontImageFileName;
        $data->back_image = $backImageFileName;
        $data->selfie_image = $selfieImageFileName;
        $data->save();

        $result = [];
        $result['success'] = [];
        $result['success']['message'] = "Successful submission. Please wait for confirmation";

        echo json_encode($result);
        return;
    }

    // helper functions
    public function checkIsProccessing ($wallet_id) {
        if(Wallets::find($wallet_id)->is_proccessing) {
            sleep(1);
            return $this->checkIsProccessing($wallet_id);
        } else {
            return true;
        }
    }

}
