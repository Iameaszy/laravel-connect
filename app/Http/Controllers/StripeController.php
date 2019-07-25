<?php

namespace App\Http\Controllers;
require __DIR__ . '/../../../vendor/autoload.php';

use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Exception\RequestException;

use GuzzleHttp\Client;

\Stripe\Stripe::setApiKey(config('app.stripe_test_secret_key'));

use App\Http\Requests\PaymentRequest;

class StripeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function charge(PaymentRequest $request)
    {
        $token = $this->getToken(20);
        $data = $request->validated();
        $charge = \Stripe\Charge::create(['amount' => (int)$data['amount']*100, 'currency' => config('app.stripe_currency'), 'source' => $data['token'],
        'transfer_group'=>$token
        ]);
        $toBeStored = [
            'tranfer_group'=> $charge->transfer_group,
            'payment_id' => $charge->id,
            'amount'=> $charge->amount
        ];
        return redirect()->route('home',$toBeStored);
    }

   public function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

       for ($i=0; $i < $length; $i++) {
           $token .= $codeAlphabet[random_int(0, $max-1)];
       }

       return $token;
   }

   public function connect(){
       $query = Request::query();
       if($query && isset($query['code'])){
           $client = new Client([
               // Base URI is used with relative requests
               'base_uri' => 'https://connect.stripe.com/oauth/',
               // You can set any number of default request options.
               'http_errors' => false
               ]);
               $response = $client->request('POST', 'token',['code'=>$query['code'],'grant_type'=>'authorization_code',
                   'client_secret' => config('app.stripe_test_secret_key'),
                   ]);
               $statusCode = $response->getStatusCode();
               $body = json_decode($response->getBody());
               if($statusCode >= 400 && $statusCode< 500){
                return (view('home',['error'=>$body->error,'error_description'=>$body->error_description]));
            }elseif($statusCode <1){
                return (view('home',['error'=>'Network Error','error_description'=>'Network error detected, try again later...']));
            }elseif($statusCode){
                (view('home',['error'=>'Server Error','error_description'=>'Error from the server, try again later...']));
            }
            $user = Auth::user();
            DB::table('users')->where('id',$user['id'])->update(['connected_id'=>$body->stripe_user_id]);
            return view('home',$body);
       } elseif ($query && isset($query['error'])){
           return view('home',$query);
       }else{
           return redirect()->route('home');
       }

   }

}