
<?php
// app/Http/Middleware/SetLocale.php


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale($request->segment(1));
        URL::defaults(['locale' => $request->segment(1)]);
        return $next($request);
    }
}

// app/Http/Kernel.php

    // ...

