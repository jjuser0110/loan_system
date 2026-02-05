<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\MaritalStatuses;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MaritalStatusController extends Controller
{
    public function index()
    {
        $marital_status = MaritalStatuses::all();
        
        return view('marital_status.index')->with('marital_status', $marital_status);
    }

    public function create()
    {
        return view('marital_status.create');
    }

    public function store(Request $request)
    {
        MaritalStatuses::create($request->all());
        return redirect()->route('marital_status.index')->withSuccess('Marital status saved');
    }

    public function edit(MaritalStatuses $marital_status)
    {
        return view('marital_status.create')->with('marital_status', $marital_status);
    }

    public function update(Request $request, MaritalStatuses $marital_status)
    {
        $marital_status->update($request->all());
        return redirect()->route('marital_status.index')->withSuccess('Marital status updated');
    }

    public function destroy(MaritalStatuses $marital_status)
    {
        $marital_status->delete();
        return redirect()->route('marital_status.index')->withSuccess('Marital status deleted');
    }
}
