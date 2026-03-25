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

    public function fetch(Request $request)
{
    try {

        $draw = $request->input('draw');
        $search = $request->input('search.value');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');

        $columns = $request->input('columns');
        $orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'id';

        // Join customer
        $query = Reference::select('references.*', 'customers.customer_name')
            ->leftJoin('customers', 'references.customer_id', '=', 'customers.id');

        // Total records
        $recordsTotal = Reference::count();

        // Search (NO EMAIL)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('references.name', 'like', "%{$search}%")
                  ->orWhere('references.new_ic', 'like', "%{$search}%")
                  ->orWhere('references.mobile', 'like', "%{$search}%")
                  ->orWhere('references.city', 'like', "%{$search}%")
                  ->orWhere('references.state', 'like', "%{$search}%")
                  ->orWhere('references.designation', 'like', "%{$search}%")
                  ->orWhere('references.reference_type', 'like', "%{$search}%")
                  ->orWhere('customers.customer_name', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        // Column mapping
        $columnMap = [
            'id'             => 'references.id',
            'customer_name'  => 'customers.customer_name',
            'reference_type' => 'references.reference_type',
            'new_ic'         => 'references.new_ic',
            'name'           => 'references.name',
            'designation'    => 'references.designation',
            'mobile'         => 'references.mobile',
            'city'           => 'references.city',
            'state'          => 'references.state',
            'created_at'     => 'references.created_at',
        ];

        $orderColumn = $columnMap[$orderColumnName] ?? 'references.id';

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($row) {
                return [
                    'id'             => $row->id,
                    'customer_id'    => $row->customer_id,
                    'customer_name'  => $row->customer_name,
                    'reference_type' => $row->reference_type,
                    'new_ic'         => $row->new_ic,
                    'name'           => $row->name,
                    'designation'    => $row->designation,
                    'mobile'         => $row->mobile,
                    'city'           => $row->city,
                    'state'          => $row->state,
                    'created_at'     => $row->created_at,
                ];
            });

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
}

}
