<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;

class InvoiceController extends Controller
{
    public function show(int $id)
    {
        $request = ServiceRequest::with(['client', 'craftsman', 'category'])->findOrFail($id);
        $user    = auth()->user();

        $canView = $user->isAdmin()
                || ($user->isClient() && $request->client_id === $user->id)
                || ($user->isCraftsman() && $request->craftsman_id === $user->id);

        abort_unless($canView, 403);

        $invoiceNumber = 'INV-' . str_pad($request->id, 6, '0', STR_PAD_LEFT);
        $invoiceDate   = $request->updated_at ?? $request->created_at;
        $siteName      = config('hirfati.site_name', 'حرفتي');
        $siteUrl       = config('hirfati.site_url', config('app.url'));
        $currency      = 'ر.ي';

        return view('pdf.invoice', compact(
            'request', 'invoiceNumber', 'invoiceDate',
            'siteName', 'siteUrl', 'currency'
        ));
    }
}
