<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Customer $customer)
    {
        $documents = $customer->documents()->latest()->get()->map(function ($doc) {
            return [
                'id'           => $doc->id,
                'file_name'    => $doc->file_name,
                'file_type'    => $doc->file_type,
                'remark'       => $doc->remark,
                'is_image'     => $doc->isImage(),
                'url'          => Storage::url($doc->file_path),
                'download_url' => route('customer.documents.download', [$doc->customer_id, $doc->id]),
                'delete_url'   => route('customer.documents.destroy',  [$doc->customer_id, $doc->id]),
                'created_at'   => $doc->created_at->format('d M Y, h:i A'),
            ];
        });

        return response()->json(['documents' => $documents]);
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'file'   => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'remark' => 'nullable|string|max:500',
        ]);

        $file      = $request->file('file');
        $ext       = strtolower($file->getClientOriginalExtension());
        $randomCode = Str::upper(Str::random(12));
        $filename  = $customer->customer_code . $randomCode . '.' . $ext;

        $stored = $file->storeAs(
            "customers/{$customer->customer_code}/document",
            $filename,
            'public'
        );

        Document::create([
            'customer_id' => $customer->id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $stored,
            'file_type'   => $file->getMimeType(),
            'remark'      => $request->remark,
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Document uploaded successfully.']);
    }

    public function download(Customer $customer, Document $doc)
    {
        abort_if($doc->customer_id !== $customer->id, 403);
        return Storage::disk('public')->download($doc->file_path, $doc->file_name);
    }

    public function destroy(Customer $customer, Document $doc)
    {
        abort_if($doc->customer_id !== $customer->id, 403);
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }

    public function updateRemark(Request $request, Customer $customer, Document $doc)
    {
        abort_if($doc->customer_id !== $customer->id, 403);

        $request->validate([
            'remark' => 'nullable|string|max:500'
        ]);

        $doc->remark = $request->remark;
        $doc->save();

        return response()->json(['success' => true]);
    }
}