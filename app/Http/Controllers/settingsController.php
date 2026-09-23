<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class settingsController extends Controller
{
    public function index(){

    $firm = Firm::findOrFail(Auth::user()->firm_id);

    return view('settings.index', compact('firm'));

    }

    public function update(Request $request){
        $firm = Firm::findOrFail(Auth::user()->firm_id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'gstin' => 'nullable|string|max:15',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        $firm->name = $validated['name'];
        $firm->gstin = $validated['gstin'];

        if($request->hasFile('logo')){

            if($firm->logo && Storage::disk('public')->exists($firm->logo)){
                Storage::disk('public')->delete($firm->logo);
            }

            $firm->logo = $request->file('logo')->store(
                'firms/logos',
                'public'
            );
        }

        $firm->save();

        return back()->with('success', 'Settings updated successfully');
    }
}
