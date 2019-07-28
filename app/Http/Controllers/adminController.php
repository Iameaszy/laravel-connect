<?php

namespace App\Http\Controllers;

use App\Payment;
use App\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

\Stripe\Stripe::setApiKey(config('app.stripe_test_secret_key'));
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $redirectTo = '/admin';
    public function __construct()
    {
        $this->registerAdmin();
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $payments = Payment::with(['buyer', 'seller'])->get();
        $sellers = User::where('category', 'seller')->where('is_admin', 0)->get();
        return view('admin.home', ['payments' => $payments, 'sellers' => $sellers]);
    }

    public function registerAdmin()
    {
        $admin_email = config('app.admin_email');
        $admin_password = config('app.admin_password');
        $user = User::where('email', $admin_email)->first();
        if (!$user) {
            return User::firstOrCreate(['email' => $admin_email], ['password' => Hash::make($admin_password), 'is_admin' => true, 'category' => 'admin']);
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'payment_id' => 'required',
            'seller_id' => 'required',
        ]);
        $payment = Payment::find($data['payment_id']);
        $seller = User::find($data['seller_id']);
        $transfer = \Stripe\Transfer::create(['amount' => (int) $data['amount'] * 100, 'currency' => config('app.stripe_currency'), 'destination' => $seller['connect_id'],
            'transfer_group' => $payment['transfer_group'],
        ]);

        $payment->seller_id = $data['seller_id'];
        $payment->save();
        return redirect()->route('admin_home');
    }
}
