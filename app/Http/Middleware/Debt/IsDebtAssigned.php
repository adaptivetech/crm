<?php

namespace App\Http\Middleware\Debt;

use Closure;
use App\Models\Setting;
use App\Models\Debt;

class IsDebtAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $debt = Debt::findOrFail($request->id);
        $settings = Setting::all();
        $isAdmin = Auth()->user()->hasRole('administrator');
        $settingscomplete = $settings[0]['lead_assign_allowed'];
        if ($isAdmin) {
            return $next($request);
        }
        if ($settingscomplete == 1  && Auth()->user()->id == $debt->fk_user_id_assign) {
            Session()->flash('flash_message_warning', 'Not allowed to create debt');
            return redirect()->back();
        }
        return $next($request);
    }
}