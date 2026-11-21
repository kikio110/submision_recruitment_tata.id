<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserSwitchController extends Controller
{
    public function __invoke(Request $r)
    {
        $u = User::findOrFail($r->validate(['user_id' => 'required|integer'])['user_id']);
        session(['demo_user_id' => $u->id]);
        return back()->with('success', "Beralih ke {$u->name}.");
    }
}
