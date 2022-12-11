<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class IsVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        foreach(User::get() as $user){
          $data=$user->remember_token;
        }
        if ($data!=$request->input('token')) {
            
            return "Your token not matched. Plz login again";
            return redirect()->route('login.post');
          }else{
        return $next($request);
          }}
}
