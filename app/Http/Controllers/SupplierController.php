<?php

namespace App\Http\Controllers;

// use Illuminate\Auth\Events\Validated;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{

    public function index(Request $request){
        $query = Supplier::where('firm_id', Auth::user()->firm_id);

        if($request->filled('search')){
            $search = $request->search;
            $query->where(function ($q) use ($search){
                $q->where('name', 'like' ,"%{$search}%")
                ->orWhere('phone' , 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest('id')->paginate(10)->withQueryString();
        return view('suppliers.index',compact('suppliers'));
    }

    public function create(?int $id = null){
        if($id){
            $supplier = Supplier::where('firm_id', Auth::user()->firm_id)
            ->findOrFail($id);
        }else{
            $supplier = new Supplier();
        }

        return view('suppliers.create',compact('supplier'));
    }

    public function update(Request $request, $id){
        $supplier = Supplier::where('firm_id', Auth::user()->firm_id)
        ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|digits:10',
            'email' => 'nullable|email',
            'address' => 'nullable|string'
        ]);

        $supplier->update($validated);

        return redirect()
        ->route('supplier.index')
        ->with('success', 'Supplier updated successfully');
    }

    public function delete($id){
        $supplier = Supplier::where('firm_id', Auth::user()->firm_id)
        ->findOrFail($id);

        if($supplier->purchases()->exists()){
            return back()->with('error', 'You cannot delete this because purchase record exists !');
        }

         $supplier->delete();

        return redirect()
        ->route('supplier.index')
        ->with('success', 'Supplier deleted successfully');
    }
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'phone' => 'required|digits:10',
            'email' => 'nullable',
            'address' => 'nullable'
        ]);

        $validated['firm_id'] = Auth::user()->firm_id;
        $supplier = Supplier::create($validated);
        return redirect()
        ->route('supplier.index')
        ->with('success', 'Supplier added successfully');
    }
}
