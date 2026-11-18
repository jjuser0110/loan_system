<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class ReferenceController extends Controller
{
    /**
     * Restrict a query (that already joins `customers`) to what the
     * logged-in user is allowed to see.
     *
     * Role 1 = Super Admin  -> everything
     * Role 2 = Branch Admin -> customers whose company belongs to the user's branch
     * Role 3 = Company Admin, Role 4 -> customers of the user's company
     *
     * NOTE: assumes `customers.company_id` exists. If your customers table
     * uses `branch_id` instead, change the case 2 / case 3 conditions.
     */
    private function scopeByRole($query)
    {
        $user = Auth::user();

        switch ($user->role_id) {
            case 1:
                break;

            case 2:
                $query->whereIn('customers.company_id', function ($q) use ($user) {
                    $q->select('id')
                      ->from('companies')
                      ->where('branch_id', $user->branch_id);
                });
                break;

            case 3:
            case 4:
                $query->where('customers.company_id', $user->company_id);
                break;

            default:
                throw new \Exception('Invalid role id.');
        }

        return $query;
    }

    /**
     * Abort with 403 if the given reference is outside the user's scope.
     */
    private function authorizeReference(Reference $reference)
    {
        $check = Reference::query()
            ->join('customers', 'references.customer_id', '=', 'customers.id')
            ->where('references.id', $reference->id);

        if (!$this->scopeByRole($check)->exists()) {
            abort(403, 'Access denied.');
        }
    }

    public function index(Request $request)
    {
        $companies = Company::orderBy('company_name')->get();

        return view('reference.index')->with('companies', $companies);
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
        $this->authorizeReference($reference);

        return view('reference.create')->with('reference', $reference);
    }

    public function update(Request $request, Reference $reference)
    {
        $this->authorizeReference($reference);

        $reference->update($request->all());
        return redirect()->route('reference.index')->withSuccess('Data updated');
    }

    public function destroy(Reference $reference)
    {
        $this->authorizeReference($reference);

        $reference->delete();

        return redirect()->route('reference.index')->withSuccess('Data deleted');
    }

    public function fetch(Request $request)
    {
        try {
            $draw   = $request->input('draw');
            $search = $request->input('search.value');
            $start  = $request->input('start', 0);
            $length = $request->input('length', 10);

            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection   = $request->input('order.0.dir', 'desc');
            if (!in_array(strtolower($orderDirection), ['asc', 'desc'])) {
                $orderDirection = 'desc';
            }

            $columns         = $request->input('columns');
            $orderColumnName = $columns[$orderColumnIndex]['data'] ?? 'id';

            // Join customer
            $query = Reference::select('references.*', 'customers.customer_name')
                ->leftJoin('customers', 'references.customer_id', '=', 'customers.id');

            // Role-based scope (applied BEFORE counting and searching)
            $this->scopeByRole($query);

            if ($request->filled('company_id')) {
                $query->where('customers.company_id', $request->company_id);
            }

            // Total records (scoped, without search)
            $recordsTotal = (clone $query)->count();

            // Search
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