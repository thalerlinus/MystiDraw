<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Raffle;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RaffleController extends Controller
{
    public function index(Request $request)
    {
        $query = Raffle::query();
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        $raffles = $query->latest()->paginate(20)->withQueryString();
        return Inertia::render('Admin/Raffles/Index', [
            'raffles' => $raffles,
            'filters' => [ 'status' => $status ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Raffles/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:raffles,slug',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'base_ticket_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3'
        ]);
        $data['status'] = 'draft';
        $data['public_stats'] = false;
        $raffle = Raffle::create($data);
        return redirect()->route('admin.raffles.edit', $raffle)->with('success','Raffle angelegt');
    }

    public function show(Raffle $raffle)
    {
        $raffle->load(['pricingTiers','items','tickets']);
        return Inertia::render('Admin/Raffles/Show', [ 'raffle' => $raffle ]);
    }

    public function edit(Raffle $raffle)
    {
        $raffle->load(['pricingTiers','items']);
        return Inertia::render('Admin/Raffles/Edit', [ 'raffle' => $raffle ]);
    }

    public function update(Request $request, Raffle $raffle)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'base_ticket_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|string|in:draft,active,finished'
        ]);
        $raffle->update($data);
        return back()->with('success','Gespeichert');
    }
}
