<?php

namespace App\Http\Controllers;
use App\Models\Categories;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(){
        $categories = Categories::where(
            'firm_id',
            Auth::user()->firm_id
        )
        ->latest()
        ->orderBy('name')
        ->get();

        $totalCategories = Categories::where('firm_id',Auth::user()->firm_id)->count();

        return view(
            'categories.index',
            compact('categories',
                    'totalCategories'        
            )
        );
    }

    public function create(?int $id = null){
        if($id){

            $category = Categories::findOrFail($id);
            return view('categories.create', compact('category'));
        }

        return view('categories.create');
    }
    public function store(Request $request){
        
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $validated['firm_id'] = Auth::user()->id;

        $categories = Categories::create($validated);

        if($categories){
            return redirect()->route('categories.index')
            ->with('success', 'Category created successfully');
        }
    }

    public function update(Categories $category, Request $request){


        $validated = $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
        ->with('success', 'Category updated successfully');

    }
}
