<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryRecovery;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryRecoveryController extends Controller
{
    public function index(Request $request)
    {
        $q = InventoryRecovery::query()
            ->with(['product:id,sku,name,thumbnail_path','user:id,name,email','raffleItem.raffle:id,title,category_id'])
            ->orderByDesc('purged_at');

        if ($search = $request->get('q')) {
            $q->whereHas('product', function($qp) use ($search){
                $qp->where('name','like',"%{$search}%")->orWhere('sku','like',"%{$search}%");
            });
        }
        if ($pid = $request->get('product_id')) {
            $q->where('product_id', $pid);
        }
        if ($uid = $request->get('user_id')) {
            $q->where('user_id', $uid);
        }

        $recoveries = $q->paginate(50)->withQueryString();
        $products = Product::orderBy('name')->get(['id','name','sku']);
        $bunnyPull = config('filesystems.disks.bunnycdn.pull_zone');
        return Inertia::render('Admin/Recoveries/Index', [
            'recoveries' => $recoveries,
            'products' => $products,
            'filters' => [
                'q' => $search,
                'product_id' => $pid,
                'user_id' => $uid,
            ],
            'bunny' => [ 'pull_zone' => $bunnyPull ],
        ]);
    }

    public function update(Request $request, InventoryRecovery $recovery)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);
        $recovery->update(['quantity' => $data['quantity']]);
        return back()->with('success','Menge aktualisiert');
    }

    public function destroy(InventoryRecovery $recovery)
    {
        $recovery->delete();
        return back()->with('success','Recovery-Eintrag gelöscht');
    }
}
