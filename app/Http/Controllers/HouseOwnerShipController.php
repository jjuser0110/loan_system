<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\HouseOwnerShip;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HouseOwnerShipController extends Controller
{
    public function index()
    {
        $house_ownership = HouseOwnerShip::all();
        return view('house_ownership.index')->with('house_ownership', $house_ownership);
    }

    public function create()
    {
        return view('house_ownership.create');
    }

    public function store(Request $request)
    {
        HouseOwnerShip::create($request->all());
        return redirect()->route('house_ownership.index')->withSuccess('House Ownership saved');
    }

    public function edit(HouseOwnerShip $house_ownership)
    {
        return view('house_ownership.create')->with('house_ownership', $house_ownership);
    }

    public function update(Request $request, HouseOwnerShip $house_ownership)
    {
        $house_ownership->update($request->all());
        return redirect()->route('house_ownership.index')->withSuccess('HouseOwnerShip updated');
    }

    public function destroy(HouseOwnerShip $house_ownership)
    {
        $house_ownership->delete();
        return redirect()->route('house_ownership.index')->withSuccess('HouseOwnerShip deleted');
    }
}
