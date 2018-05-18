<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Markets;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class HomeController extends Controller
{
    public function index () {
        return view('home');
    }

    public function markets () {
        $data['title'] = "All Markets";
        return view('markets')->with('data', $data);
    }

    public function trade ($market_id) {
        $data = Markets::find($market_id)->toArray();
        $data['title'] = $data['name'];
        return view('trade')->with('data',$data);
    }

    public function wallets () {
        if (!Session::has('user')) {
            Session::flash('error', 'You must login to access');
            return redirect('user/login/wallets');
        }   

        $user = Session::get('user');

        if (!Session::get('user_two_auth')){
            Session::flash('error', 'Please check Two-factory Authentication');
            return redirect('user/loginTwoAuth/wallets');
        } 


        $data['title'] = "Wallets";
        return view('wallets')->with('data',$data);
    }
}