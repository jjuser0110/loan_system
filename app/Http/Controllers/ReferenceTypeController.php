<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\ReferenceType;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ReferenceTypeController extends Controller
{
    public function index()
    {
        $reference_type = ReferenceType::all();
        return view('reference_type.index')->with('reference_type', $reference_type);
    }

    public function create()
    {
        return view('reference_type.create');
    }

    public function store(Request $request)
    {
        ReferenceType::create($request->all());
        return redirect()->route('reference_type.index')->withSuccess('ReferenceType saved');
    }

    public function edit(ReferenceType $reference_type)
    {
        return view('reference_type.create')->with('reference_type', $reference_type);
    }

    public function update(Request $request, ReferenceType $reference_type)
    {
        $reference_type->update($request->all());
        return redirect()->route('reference_type.index')->withSuccess('ReferenceType updated');
    }

    public function destroy(ReferenceType $reference_type)
    {
        $reference_type->delete();
        return redirect()->route('reference_type.index')->withSuccess('ReferenceType deleted');
    }
}
