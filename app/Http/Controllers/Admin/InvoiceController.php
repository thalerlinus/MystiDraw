<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Payment::query()->whereNotNull('invoice_number');
        if ($search = $request->get('q')) {
            $q->where(function($qq) use ($search){
                $qq->where('invoice_number','like',"%$search%")
                   ->orWhere('provider_txn_id','like',"%$search%")
                   ->orWhereHas('order.user', function($u) use ($search){
                       $u->where('email','like',"%$search%")
                         ->orWhere('name','like',"%$search%");
                   });
            });
        }
        $payments = $q->with(['order.user'])->latest('paid_at')->paginate(40)->withQueryString();
        return Inertia::render('Admin/Invoices/Index', [
            'payments' => $payments,
            'filters' => [ 'q' => $search ]
        ]);
    }

    public function download(Payment $payment)
    {
        abort_unless($payment->invoice_number, 404);
        $payment->load(['order.items.raffle.category','order.user']);
        $pdf = Pdf::loadView('pdf.invoice', ['payment' => $payment])->setPaper('a4');
        $file = 'Rechnung_' . ($payment->invoice_number) . '.pdf';
        return $pdf->stream($file); // inline Anzeige mit korrekten PDF-Headern
    }
}
