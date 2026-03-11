<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Reference;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        $reference = Reference::all();

        return view('reference.index')->with('reference',$reference);
    }

    public function create()
    {
        return view('reference.create');
    }

    public function store(Request $request)
    {
        $reference = Reference::create($request->all());

        return redirect()->route('reference.index')->withSuccess('Data saved');
    }

    public function edit(Reference $reference)
    {
        return view('reference.create')->with('reference',$reference);
    }

    public function update(Request $request, Reference $reference)
    {
        $reference->update($request->all());
        return redirect()->route('reference.index')->withSuccess('Data updated');
    }

    public function destroy(Reference $reference)
    {
        $reference->delete();

        return redirect()->route('reference.index')->withSuccess('Data deleted');
    }

}
