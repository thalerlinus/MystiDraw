<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->get('q',''));
        if($q === ''){
            return response()->json([]);
        }
        $users = User::query()
            ->where(function($w) use ($q){
                $w->where('name','LIKE',"%{$q}%")
                  ->orWhere('email','LIKE',"%{$q}%");
            })
            ->orderBy('name')
            ->limit(25)
            ->get(['id','name','email']);
        return response()->json($users);
    }
}
