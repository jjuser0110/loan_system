<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Race;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RaceController extends Controller
{
    public function index()
    {
        $race = Race::all();
        return view('race.index')->with('race', $race);
    }

    public function create()
    {
        return view('race.create');
    }

    public function store(Request $request)
    {
        Race::create($request->all());
        return redirect()->route('race.index')->withSuccess('Race saved');
    }

    public function edit(Race $race)
    {
        return view('race.create')->with('race', $race);
    }

    public function update(Request $request, Race $race)
    {
        $race->update($request->all());
        return redirect()->route('race.index')->withSuccess('Race updated');
    }

    public function destroy(Race $race)
    {
        $race->delete();
        return redirect()->route('race.index')->withSuccess('Race deleted');
    }
}
