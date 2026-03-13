<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\ExpensesType;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ExpensesTypeController extends Controller
{
    public function index()
    {
        $expenses_type = ExpensesType::all();
        return view('expenses_type.index')->with('expenses_type', $expenses_type);
    }

    public function create()
    {
        return view('expenses_type.create');
    }

    public function store(Request $request)
    {
        ExpensesType::create($request->all());
        return redirect()->route('expenses_type.index')->withSuccess('ExpensesType saved');
    }

    public function edit(ExpensesType $expenses_type)
    {
        return view('expenses_type.create')->with('expenses_type', $expenses_type);
    }

    public function update(Request $request, ExpensesType $expenses_type)
    {
        $expenses_type->update($request->all());
        return redirect()->route('expenses_type.index')->withSuccess('ExpensesType updated');
    }

    public function destroy(ExpensesType $expenses_type)
    {
        $expenses_type->delete();
        return redirect()->route('expenses_type.index')->withSuccess('ExpensesType deleted');
    }
}
