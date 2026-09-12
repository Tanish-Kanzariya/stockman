<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(){
        return view('auth.register');
    }

    public function login(){
        return view('auth.login');
    }

    public function authenticate(Request $request){

    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if(Auth::attempt($credentials, $request->boolean('remember'))){
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success','Logged in successfully');
    }

    return back()
    ->withErrors(['email' => 'The provided credentials are incorrect.'])
    ->onlyInput('email');
    }

    public function store(Request $request){
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'firm_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:6|confirmed'
    ]);

    $user = DB::transaction(function () use ($validated){
        $firm = Firm::create([
            'name' => $validated['firm_name'],
            'gstin' => null
        ]);

        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'firm_id' => $firm->id  
        ]);
    });

        Auth::login($user); 

        $request->session()->regenerate();

        return redirect()->route('dashboard')
        ->with('success', 'Account created successfully');
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
