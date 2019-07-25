<?php
use Config;
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $query = Request::query();
        if ($query && isset($query['code'])) {
            $client = new Client([
                // You can set any number of default request options.
                'http_errors' => false,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);
            $key = config('app.stripe_test_secret_key');
            $response = $client->post('https://connect.stripe.com/oauth/token', ['body'=>json_encode(['code' => $query['code'], 'grant_type' => 'authorization_code',
                'client_secret' => $key
            ])]);
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);
            if ($statusCode >= 400 && $statusCode < 500) {
                return (view('home', ['error' => $body['error'], 'error_description' => $body['error_description']]));
            } elseif ($statusCode < 1) {
                return (view('home', ['error' => 'Network Error', 'error_description' => 'Network error detected, try again later...']));
            } elseif ($statusCode) {
                (view('home', ['error' => 'Server Error', 'error_description' => 'Error from the server, try again later...']));
            }
            $user = Auth::user();
            DB::table('users')->where('id', $user['id'])->update(['connect_id' => $body['stripe_user_id']]);
            return view('home', $body);
        } elseif ($query && isset($query['error'])) {
            return view('home', $query);
        } else {
            return view('home', $query);
        }
    }
}