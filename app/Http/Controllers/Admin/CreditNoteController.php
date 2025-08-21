<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $q = Payment::query()->whereNotNull('credit_note_number');
        if ($search = $request->get('q')) {
            $q->where(function($qq) use ($search){
                $qq->where('credit_note_number','like',"%$search%")
                   ->orWhere('provider_txn_id','like',"%$search%")
                   ->orWhereHas('order.user', function($u) use ($search){
                        $u->where('email','like',"%$search%")
                           ->orWhere('name','like',"%$search%" );
                   })
                   ->orWhereHas('user', function($u) use ($search){
                        $u->where('email','like',"%$search%")
                           ->orWhere('name','like',"%$search%" );
                   });
            });
        }
        $payments = $q->with(['order.user','user'])->latest('refund_email_sent_at')->paginate(40)->withQueryString();
        return Inertia::render('Admin/CreditNotes/Index', [
            'payments' => $payments,
            'filters' => ['q' => $search]
        ]);
    }

    public function download(Payment $payment)
    {
        abort_unless($payment->credit_note_number, 404);
        $payment->load(['order.items.raffle.category','order.user','user']);
        $pdf = Pdf::loadView('pdf.credit-note', ['payment' => $payment])->setPaper('a4');
        $file = 'Gutschrift_' . ($payment->credit_note_number) . '.pdf';
        return $pdf->stream($file); // Inline Ausgabe mit richtigen PDF-Headern
    }
}
