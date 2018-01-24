<?php

namespace App\Http\Middleware\Debt;

use Closure;

class CanDebtCreate
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
        if (!auth()->user()->can('debt-create')) {
            Session()->flash('flash_message_warning', 'Not allowed to create debt');
            return redirect()->route('debts.index');
        }
        return $next($request);
    }
}
