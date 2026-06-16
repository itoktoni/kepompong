<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessMiddleware
{
    protected array $abilityMap = [
        'getTable' => 'view',
        'getCreate' => 'save',
        'postCreate' => 'save',
        'getUpdate' => 'save',
        'postUpdate' => 'save',
        'postDelete' => 'delete',
        'postDeleteBulk' => 'delete',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $user = Auth::user();

        // if (!$user) {
        //     return $next($request);
        // }

        // $role = $user->role;
        // $module = request()->route()->getAction('name');
        // $method = request()->route()->getActionMethod();
        // $ability = $this->abilityMap[$method] ?? null;

        // $restrict = config('permision');

        // if(isset($restrict[$role][$module]))
        // {
        //     if(is_bool($restrict[$role][$module]) && $restrict[$role][$module])
        //     {
        //         abort(403, ERROR_PERMISION);
        //     }

        //     if(is_array($restrict[$role][$module]))
        //     {
        //         $permision = $restrict[$role][$module];
        //         if(!empty($ability) && in_array($ability, $permision))
        //         {
        //             abort(403, ERROR_PERMISION);
        //         }
        //     }
        // }

        return $next($request);
    }
}
