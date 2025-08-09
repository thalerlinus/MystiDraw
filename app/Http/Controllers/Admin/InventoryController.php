<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserInventory;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        $items = UserInventory::with(['product','user'])->orderByDesc('id')->paginate(50);
        return Inertia::render('Admin/Inventory/Index', [ 'items' => $items ]);
    }
}
