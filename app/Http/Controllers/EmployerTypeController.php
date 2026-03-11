<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\EmployerType;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployerTypeController extends Controller
{
    public function index()
    {
        $employer_type = EmployerType::all();
        return view('employer_type.index')->with('employer_type', $employer_type);
    }

    public function create()
    {
        return view('employer_type.create');
    }

    public function store(Request $request)
    {
        EmployerType::create($request->all());
        return redirect()->route('employer_type.index')->withSuccess('EmployerType saved');
    }

    public function edit(EmployerType $employer_type)
    {
        return view('employer_type.create')->with('employer_type', $employer_type);
    }

    public function update(Request $request, EmployerType $employer_type)
    {
        $employer_type->update($request->all());
        return redirect()->route('employer_type.index')->withSuccess('EmployerType updated');
    }

    public function destroy(EmployerType $employer_type)
    {
        $employer_type->delete();
        return redirect()->route('employer_type.index')->withSuccess('EmployerType deleted');
    }
}
