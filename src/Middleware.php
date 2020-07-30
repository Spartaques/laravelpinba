<?php


namespace Spartaques\LaravelPinba;


use Closure;

class Middleware
{
    const UNKNOWN_SCRIPT_NAME = 'unknown';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $return = $next($request);

        if (extension_loaded('pinba')) {
            $script_name = $request->fullUrl();
            pinba_script_name_set($script_name);
        }

        return $return;
    }
}
