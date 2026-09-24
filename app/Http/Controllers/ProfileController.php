<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Firm;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchase_item;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return;
use App\Models\Sale_return_item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public function destroy(Request $request){
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string'
        ]);

        if(!Hash::check($request->current_password, $user->password)){
            return back()->withErrors([
                'delete_password' => 'Current password is incorrect.'
            ]);
        }

        $firm = $user->firm;

        if(!$firm){
            return back()->withErrors([
                'delete_account' => 'Firm not found.'
            ]);
        }

        $otherUser = $firm->users()->where('id', '!=', $user->id)->exists();

        if($otherUser){
            return back()->withErrors([
                'delete_account' => 'Account cannot be deleted because other users belongs to this firm.'
            ]);
        }

        $logo = $firm->logo;

        DB::transaction(function() use ($firm){

            // SALES

            $saleIds = Sale::where('firm_id', $firm->id)->pluck('id');

            if($saleIds->isNotEmpty()){
                $saleItemIds = Sale_item::whereIn('sale_id', $saleIds)->pluck('id');

                if($saleItemIds->isNotEmpty()){
                    Sale_return_item::whereIn('sale_item_id', $saleItemIds)->delete();
                }

                Sale_return_item::whereIn(
                    'sale_return_id',
                    Sale_return::whereIn('sale_id', $saleIds)->pluck('id')
                )->delete();

                Sale_return::whereIn('sale_id', $saleIds)->delete();

                Sale_item::whereIn('sale_id', $saleIds)->delete();

                Sale::whereIn('id', $saleIds)->delete();
            }


            //Purchase

            $purchaseIds = Purchase::where('firm_id', $firm->id)->pluck('id');

            if($purchaseIds->isNotEmpty()){
                Purchase_item::whereIn('purchase_id', $purchaseIds)->delete();

                Purchase::whereIn('id', $purchaseIds)->delete();
            }

            //Stock Movements

            StockMovement::where('firm_id', $firm->id)->delete();

            //Products

            Product::where('firm_id', $firm->id)->delete();

            //Categories

            Categories::where('firm_id', $firm->id)->delete();

            //Suppliers

            Supplier::where('firm_id', $firm->id)->delete();

            //Firm

            $firm->delete();
        });

        if($logo && Storage::disk('public')->exists($logo)){
            Storage::disk('public')->delete($logo);
        }

        //Logout

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
        ->with('success', 'Your stockman account is permanently deleted.');
    }
}
