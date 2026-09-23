<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        $user = Auth::user();

        return view('profile.index', compact('user'));
    }

    public function update(Request $request){
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'current_password' => 'nullable|string',

            'password' => ['nullable', 'string','min:6', 'confirmed']
        ]);

        $user->name = $validated['name'];

        if(!empty($validated['password'])){

            if(empty($validated['current_password'])){
                return back()->withErrors(['current_password' => 'Current Password is required.'])
                ->withInput();
            }

            if(!Hash::check(
                $validated['current_password'], $user->password
            )){
                return back()->withErrors([
                    'current_password' => 'Current Password is incorrect.'
                ])->withInput();
            }

            $user->password = $validated['password'];
        }

        $user->save();

        return back()->with('success', 'Profile Updated Successfully');
    }
}
