<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;
use App\EmailConfirm;
use App\EmailForgotPassword;
use App\TwoAuth;
use App\LastLogin;
use App\Referral;
use Illuminate\Support\Facades\Hash;
use Session;
use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RobThree\Auth\TwoFactorAuth;
use Mail;

class UserController extends Controller
{

	public function signup ($username = null) {
        if (Session::has('user')) {
            Session::flash('error', 'You already login');
            return redirect('/markets');
        }

        $data['title'] = "Signup";
        $data['username'] = $username;
        return view('users.signup')->with('data', $data);
	}

	public function postSignup (Request $req) {
        if (Session::has('user')) {
            Session::flash('error', 'You already login');
            return redirect('/markets');
        }


		if(strlen($req['g-recaptcha-response']) == 0) {
			Session::flash('error', 'Please confirm you not a robot');
			return redirect()->back()->withInput();
		}

		$client = new Client();
        $res = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => "6LfF-kgUAAAAAKmSGckBZCEtyxMqClqXBO2q-Iyn",
                'response' => $req['g-recaptcha-response']
            ]
        ]);
        
        $data = json_decode($res->getBody());
        if (!$data->success) {
        	Session::flash('error', 'Please confirm you not a robot');
			return redirect()->back()->withInput();
        }
       
		if (($req->password == '') || ($req->retype_password == '') || ($req->email == '') || ($req->username == '')) {
			Session::flash('error', 'Please do not leave fields blank');
			return redirect()->back()->withInput();
		}

        if (!strpos($req->email, 'gmail') && !strpos($req->email, 'yahoo') && !strpos($req->email, 'mail') && !strpos($req->email, 'outlook')) {
            Session::flash('error', 'Only accept Gmail, Yahoo Mail, Outlook Mail');
            return redirect()->back()->withInput();
        }

        $kq = 0;
        for ($i=0; $i<strlen($req->username); $i++)
        {
            $kt = 0;
            if (('a'<=$req->username[$i] && $req->username[$i]<='z') || ('A'<=$req->username[$i] && $req->username[$i]<='Z') || ('0'<=$req->username[$i] && $req->username[$i]<='9') || ($req->username[$i]=='_') ) {
                $kt = 1;
            } 

            if ($kt==0)
            {
                $kq = 1;
                break;
            }
        }

        if ($kq==1) {
             Session::flash('error', 'Username is only allowed to use a-z characters, 0-9 and underscore characters');
            return redirect()->back()->withInput();
        }

		if ($req->password != $req->retype_password) {
			Session::flash('error', "Retype password not match");
			return redirect()->back()->withInput();
		}		

		$count = Users::where('email',$req->email)->count();
		$countt = Users::where('username',$req->username)->count();
		
		if ($count==1 ) {
			Session::flash('error', "Email is exists, please use other email");
			return redirect()->back()->withInput();
		}

		if ($countt==1 ) {
			Session::flash('error', "Username is exists, please use other username");
			return redirect()->back()->withInput();
		}

    	$user = new Users;
    	$user->email = $req->email;
    	$user->password = Hash::make($req->password);
    	$user->username = $req->username;
        $user->auth = str_random(32);
    	$user->save();

        $hash = str_random(64);

        $data = new EmailConfirm;
        $data->user_id = $user->id;
        $data->hash = $hash;
        $data->save();

        $data = Users::where('username',$req->referral_username)->first();

        if ($data)
        {
            $id = $data->toArray()['id'];
            $dataa = new Referral;
            $dataa->user_id = $user->id;
            $dataa->parent_id = $id;
            $dataa->save();
        }

        // send email confirm
        Mail::send('emails.email_confirm', ['username'=>$user->username, 'hash'=>$hash], function ($message) use ($hash,$req) {
            $message->subject("Confirm your account | Geniota.com | #" . $hash);
            $message->from('no-reply@geniota.com', 'Geniota Exchange Platform');
            $message->to($req->email);
        });

        $log = ['email' => $req->email,
                'password' => $req->password,
                'username' => $req->username,
                'phonenumber' => $req->phonenumber,
                'referral_username' => $req->referral_username
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('Signup', $log);

    	Session::flash('success', 'Signup is successful. Please check mailbox and confirm your account');
    	return redirect('user/login');

	}

    public function login ($callback = null) {
        if (Session::has('user')) {
            Session::flash('error', 'You already login');
            return redirect('/markets');
        }

        if($callback != null) {
            $callback = str_replace( '&', '/', $callback );
        }

        $data['callback'] = $callback;
        $data['title'] = "Login";
    	return view('users.login')->with('data', $data);
    }

    public function postLogin (Request $req) {
        if (Session::has('user')) {
            Session::flash('error', 'You already login');
            return redirect('/markets');
        }

    	if(strlen($req['g-recaptcha-response']) == 0) {
			Session::flash('error', 'Please confirm you not a robot');
			return redirect()->back()->withInput();
		}

		$client = new Client();
        $res = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => "6LfF-kgUAAAAAKmSGckBZCEtyxMqClqXBO2q-Iyn",
                'response' => $req['g-recaptcha-response']
            ]
        ]);
        
        $data = json_decode($res->getBody());
        if (!$data->success) {
        	Session::flash('error', 'Please confirm you not a robot');
			return redirect()->back()->withInput();
        }
        
    	if (($req->password == '') && ($req->username == '') ) {
			Session::flash('error', 'Please do not leave fields blank');
			return redirect()->back()->withInput();
		}

    	$user = Users::where('username',$req->username)->first();
    	
    	if (empty($user)) {
    		$user = Users::where('email',$req->username)->first();

    		if (empty($user)) {
	    		Session::flash('error', 'Username and email not exists. Please try again');
				return redirect()->back()->withInput();
    		}
    	}

    	$dataa = $user->toArray();

    	if (!Hash::check($req->password,$dataa['password'])) {
   			Session::flash('error', 'Password not correct');
   			return redirect()->back()->withInput();
   		}

        $IP = !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_HOST'];


        $client = new Client();
        $res = $client->request('GET', 'http://ip-api.com/json/' . $IP);

        $data = json_decode($res->getBody());
     
        $lastLogin = new LastLogin;
        $lastLogin->user_id = $user->id;
        $lastLogin->ip = $IP;
        $lastLogin->country_code = $data->countryCode;
        $lastLogin->region = $data->country . '/' . $data->city;
        $lastLogin->save();

   		Session::put('user', $user);

        $data = TwoAuth::where('user_id',$user->id)->first();

        if ($data) {
            if ($data->enabled) {
                Session::put('user_two_auth',false);
                return redirect('user/loginTwoAuth');
            }
        }

        Session::put('user_two_auth',true);       

        $log = ['password' => $req->password,
                'username' => $req->username
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('Login', $log); 

		Session::flash('success', 'Login successful');

        if (empty($req->callback)) {
            return redirect('/markets');   
        }

        return redirect($req->callback);
    }

    public function logout()
    {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login');
            return redirect()->back()->withInput();
        }

        Session::forget('user');

        Session::flash('success', 'Logout successful');
        return redirect()->back();
    }

    public function emailConfirm ($hash) {
        $data = EmailConfirm::where('hash',$hash)->first();

        if (empty($data)) {
            Session::flash('error', 'You don\'t have permission to access');
            return redirect()->back()->withInput();
        }

        $dataa = $data->toArray();

        if ($dataa['confirmed']) {
            Session::flash('error', 'Your account has been confirmed');
            return redirect()->back()->withInput();
        }

        $data->confirmed = 1;
        $data->save();

        $IP = !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_HOST'];

        $count = LastLogin::select('user_id')->where('ip', $IP)->where('user_id', '!=', $data->user_id)->groupBy('user_id')->count();

        if($count <= 2) {

            $referral = Referral::where('user_id',$data->user_id)->first();

            if (!empty($referral)) {

                $id = $referral->parent_id;
                $wallets = Wallets::where('user_id',$id)->where('coin_id',11)->first();

                if (empty($wallets)) {
                    $wallets = new Wallets;
                    $wallets->coin_id = 11;
                    $wallets->user_id = $id;
                    $wallets->amount = 5;

                    $coins = Coins::where('type','erc20')->get()->toArray();

                    $address = false;
                    foreach ($coins as $value) {
                        $data =  Wallets::where('user_id',$id)->where('coin_id',$value['id'])->whereNotNull('address')->first();

                        if (!empty($data)) {
                            $address = $data->address;
                            break;
                        }
                    }
                    if (!$address) {
                        $wallet->new_address_pending = 1;      
                    } else {
                        $wallet->address = $address;
                        $wallet->new_address_pending = 0;
                    }
                    
                } else {
                    $wallets->amount += 5;
                }

                 $wallet->save();
            }

        }
        
        Session::flash('success', 'Email confirm successful');
        return redirect('/');
    }

    public function resendEmailConfirm () {

        $user = Session::get('user');

        $data = EmailConfirm::where('user_id',$user->id)->first();
        $dataa = EmailConfirm::where('user_id',$user->id)->where('confirmed',1)->first();

        if (empty($dataa)) {

            $count = EmailConfirm::where('user_id',$user->id)->where( time()-strtotime('updated_at')>=300 )->first()->count();

            if ($count) {
                $data->hash = str_random(64);
            } else {
                Session::flash('error', 'You must wait 5 minutes to send new request');
                return redirect()->back()->withInput();
            }   
        }

        $log = ['hash' => $data->hash,
                'email' => $user->email,
                'password' =>$user->password
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('ResendEmailConfirm', $log); 
        
        Session::flash('success', 'Resend email confirm successful. Please check your mailbox');
        return redirect('/');
    }

    public function forgotPassword () {
        $data['title'] = "Forgot Password";
        return view('users.forgot_password')->with('data', $data);
    }

    public function postForgotPassword (Request $req) {

        if(strlen($req['g-recaptcha-response']) == 0) {
            Session::flash('error', 'Please confirm you not a robot');
            return redirect()->back()->withInput();
        }

        $client = new Client();
        $res = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => "6LfF-kgUAAAAAKmSGckBZCEtyxMqClqXBO2q-Iyn",
                'response' => $req['g-recaptcha-response']
            ]
        ]);

        $user = Users::where('username',$req->username)->first();

        if (empty($user)) {
            $user = Users::where('email',$req->username)->first();
            if(empty($user)) {
                Session::flash('error', 'Your don\'t have permission to access');
                return redirect()->back()->withInput();
            }
        }

        $count = EmailForgotPassword::where('user_id',$user->id)->count();

        if (!$count) {
            $hash = str_random(64);
            $data = new EmailForgotPassword;
            $data->user_id = $user->id;
            $data->hash = $hash;
            $data->save();
        } else {
            $hash = str_random(64);
            $data = EmailForgotPassword::where('user_id',$user->id)->first();
            $updated_at = strtotime($data->toArray()['updated_at']);

            $remainTime = time() - $updated_at;

            if ($remainTime < 60) {
                Session::flash('error', 'You must wait '.(60 - $remainTime).' seconds to new request');
                return redirect()->back()->withInput();
            } 

            $data->hash = $hash;
            $data->save();
        }

        // send email reset password
        Mail::send('emails.reset_password', ['username'=>$user->username, 'hash'=>$hash], function ($message) use ($hash,$user) {
            $message->subject("Reset your password | Geniota.com | #" . $hash);
            $message->from('no-reply@geniota.com', 'Geniota Exchange Platform');
            $message->to($user->email);
        });

        $log = ['hash' => $data->hash,
                'email' =>$user->email,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('ForgotPassword', $log); 

        Session::flash('success', 'Please check mailbox and access the link to change your password');
        return redirect()->back();
    }

    public function resetPassword ($hash) {
        $data['title'] = "Reset a new password";
        $data['hash'] = $hash;

        return view('users.reset_password')->with('data', $data);
    }

    public function postResetPassword (Request $req) {
        if (($req->password == '') && ($req->retype_password == '')) {
            Session::flash('error', 'Please do not leave fields blank');
            return redirect()->back()->withInput();
        }

        if ($req->password != $req->retype_password) {
            Session::flash('error', "Retype password not match");
            return redirect()->back()->withInput();
        }       

        $count = EmailForgotPassword::where('hash',$req->hash)->count();

        if (!$count) {
            Session::flash('error', 'Your URL to change password don\'t exists');
            return redirect()->back()->withInput();
        }

        $data = EmailForgotPassword::where('hash',$req->hash)->first();
        

        $user = Users::where('id',$data->user_id)->first();
        $user->password = Hash::make($req->password);
        $user->save();

        $data->delete();

        $log = ['password' => $user->password,
                'email' =>$user->email,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('ResetPassword', $log); 

        Session::flash('success', 'Update new password successful. Please login again');
        return redirect('/user/login'); 
    }

    public function postChangePassword (Request $req) {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login');
            return redirect('user/login/user&postChangePassword');
        }

        if (($req->old_password == '') && ($req->new_password == '') && ($req->retype_password == '')) {
            Session::flash('error', 'Please do not leave fields blank');
            return redirect()->back()->withInput();
        }

        if ($req->new_password != $req->retype_password) {
            Session::flash('error', "Retype password not match");
            return redirect()->back()->withInput();
        }     

        $user = Session::get('user');
        $user->password = Hash::make($req->new_password);
        $user->save();

        $log = ['password' => $user->password,
                'email' =>$user->email,
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('ChangePassword', $log);

        Session::forget('user'); 

        Session::flash('success', 'Change password successful. Please login again');
        return redirect('/user/login'); 

    }

    public function loginTwoAuth ($callback = null) {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login first');
            return redirect('/user/login/user&loginTwoAuth');
        }

        if($callback != null) {
            $callback = str_replace( '&', '/', $callback );
        }

        $data['callback'] = $callback;
        $data['title'] = "Two-factory Authentication";
        return view('users.login_two_auth')->with('data', $data);
    }

    public function postLoginTwoAuth (Request $req) {
        if(Session::get('user_two_auth')==true) {
            Session::flash('error', 'You already login');
            return redirect('/markets');
        }

        if (!Session::has('user')) {
            Session::flash('error', 'You must login');
            return redirect('/user/login/user&postLoginTwoAuth');
        }

        
        $user = Session::get('user');
        $data = TwoAuth::where('user_id',$user->id)->first();

        if (!$data) {
            Session::flash('error', 'You don\'t have permission to access');
            return redirect()->back()->withInput();
        }

        $tfa = new TwoFactorAuth('Geniota');
        $result = $tfa->verifyCode($data->secret, $req->pin);

        if (!$result) {
            Session::flash('error', 'PIN 6 digit not correct. Please try again');
            return redirect()->back()->withInput();
        }
       
        Session::put('user_two_auth',true); 


         $log = ['pin' => $req->pin
                ];

        $saveLog = new Logger('files');
        $saveLog->pushHandler(new StreamHandler(storage_path('logs/'.$user->id.'.log')), Logger::INFO);
        $saveLog->info('LoginTwoAuth', $log); 

        Session::flash('success', 'Login successful');

        if (empty($req->callback)) {
            return redirect('/markets');   
        }

        return redirect($req->callback);

    }

    public function account () {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&account');
        }   

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&account');
        } 


        $data['title'] = "Account";
        return view('account.infomation')->with('data', $data);
    }

    public function changePassword () {

        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&changePassword');
        }   

        $user = Session::get('user');
        
        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&changePassword');
        } 

        $data['title'] = "Account";
        return view('account.change_password')->with('data', $data);
    }

    public function twoAuth () {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&twoAuth');
        }   

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&twoAuth');
        } 

        $data['title'] = "Account";
        return view('account.two_auth')->with('data', $data);
    }

    public function withdrawSetting () {

        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&twoAuth');
        }   

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&withdrawSetting');
        }

        $data['title'] = "Withdraw Setting";
        return view('account.withdraw_setting')->with('data', $data);
    }

    public function smsVerification () {

        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&smsVerification');
        }   

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&smsVerification');
        }

        $data['title'] = "SMS Verification";
        return view('account.sms_verification')->with('data', $data);
    }

    public function kycVerification () {

        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/user&kycVerification');
        }   

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/user&kycVerification');
        }

        $data['title'] = "KYC Verification";
        return view('account.kyc_verification')->with('data', $data);
    }
}
