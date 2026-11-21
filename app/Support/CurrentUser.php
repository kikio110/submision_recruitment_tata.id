<?php
namespace App\Support;
use App\Models\User;
/** Demo user selector (SRS FR-015): no real authentication. */
class CurrentUser {
    private static ?User $override = null;
    public static function set(?User $u): void { self::$override = $u; }
    public static function get(): ?User {
        if (self::$override) return self::$override;
        if (! app()->bound('session') || ! request()->hasSession()) return null;
        $id = session('demo_user_id');
        return $id ? User::find($id) : User::where('role', 'DIRECTOR')->first();
    }
    public static function id(): ?int { return self::get()?->id; }
    public static function name(): string { return self::get()?->name ?? 'System'; }
}
