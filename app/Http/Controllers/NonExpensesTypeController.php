<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\NonExpensesType;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class NonExpensesTypeController extends Controller
{
    public function index()
    {
        $non_expenses_type = NonExpensesType::all();
        return view('non_expenses_type.index')->with('non_expenses_type', $non_expenses_type);
    }

    public function create()
    {
        return view('non_expenses_type.create');
    }

    public function store(Request $request)
    {
        NonExpensesType::create($request->all());
        return redirect()->route('non_expenses_type.index')->withSuccess('NonExpensesType saved');
    }

    public function edit(NonExpensesType $non_expenses_type)
    {
        return view('non_expenses_type.create')->with('non_expenses_type', $non_expenses_type);
    }

    public function update(Request $request, NonExpensesType $non_expenses_type)
    {
        $non_expenses_type->update($request->all());
        return redirect()->route('non_expenses_type.index')->withSuccess('NonExpensesType updated');
    }

    public function destroy(NonExpensesType $non_expenses_type)
    {
        $non_expenses_type->delete();
        return redirect()->route('non_expenses_type.index')->withSuccess('NonExpensesType deleted');
    }
}
