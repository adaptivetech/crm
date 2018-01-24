<?php

namespace App\Http\Middleware\Debt;

use Closure;
use App\Models\Setting;
use App\Models\Debt;

class CanDebtUpdateStatus
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
        $isAdmin = Auth()->user()->hasRole('administrator');

        $settings = Setting::all();
        if ($isAdmin) {
            return $next($request);
        }
        $settingscomplete = $settings[0]['lead_complete_allowed'];
        if ($settingscomplete == 1  && Auth()->user()->id == $debt->fk_user_id_assign) {
            Session()->flash('flash_message_warning', 'Only assigned user are allowed to close debt.');
            return redirect()->back();
        }
        return $next($request);
    }
}
