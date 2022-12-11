<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\UserRequest;
use App\Models\UserVerify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use function Termwind\render;

class UserController extends Controller
{
               // Login Method
    public function postLogin(LoginRequest $request)
    { 
       try
       {
           $req=$request->safe()->only(['email', 'password']);
           if($req)
           {
                $user=User::where('email',$req['email'])->get();
                foreach($user as $get)
                {
                    $gets=$get->password;
                }
                $password=hash::check($req['password'],$gets);
                if($password)
                {
                   $rememberMe = Str::random(10); 
                   $updateUser=User::where('email',$req['email'])->update([
                            'remember_token'=>$rememberMe
                         ]); 
                   if($updateUser)
                    {            
                        return redirect()->route('dashboard');
                    }
                }
                else{
                    return "invalid password";
                }
            }
           else{
                    return redirect("login.post")->withSuccess('Oppes! You have entered invalid credentials');
                }
        }
       catch(\App\Exceptions\UserNotFoundException $exception){
        report($exception);
        render($exception);
        return response()->exception();
        }
    }
         // SignUp Method
    public function signUp(UserRequest $request)
    {  
        try
        {
            $data=$request->validated();
            $password=Hash::make($data['password'], [
            'rounds' => 12,
             ]);
            $createUser =User:: create([
            'name'=>$data['name'],
           'email'=>$data['email'],
          'password'=>$password,
          'address'=>$data['address'],
           'image'=>$data['image'],
            ]);
            if ($data->hasFile('image')) 
            {
                //  $path = Storage::disk('local')->put($request->file('photo')->getClientOriginalName(),$request->file('photo')->get());
              $path = $data->file('image')->store('/images');
             }
            $token = Str::random(10);
            $link="http://localhost:8000/api/account/verify/$token";
  
            UserVerify::create([
              'user_id' => $createUser->id, 
              'token' => $token
            ]);
           Mail::send('emailVerification', ['link' => $link], function($message) use($request){
              $message->to($request->email);
              $message->subject('Email Verification Mail');
            });
           return response()->json([
            "message"=>"You are registered successfully. Now go to ur gmail for account verification"
            ]);
        }
        catch(\App\Exceptions\UserNotFoundException $exception){
            report($exception);
            render($exception);
            return response()->exception();
        }
    }
      // Dashboard method
    public function dashboard(Request $request)
    {
        try
        {
            $user=User::where('remember_me',$request->input('token'))->first();
            if($user){
                return response()->json($user);
            }
            else{
           return redirect()->route("login");
            }
        }
       catch(\App\Exceptions\UserNotFoundException $exception){
        report($exception);
        render($exception);
        return response()->exception();
         }
    }
      // Email Verfifed Method
    public function emailVerify($token)
    {
        try
        {
            $verifyUser = UserVerify::where('token', $token)->first();
            if(!is_null($verifyUser) )
            {
               $user = $verifyUser->user;   
               if(!$user->email_verified_at) 
               {
                   $verifyUser->user->email_verified_at = time();
                    $verifyUser->user->save();
                    $message = "Your e-mail is verified. You can now login.";
                   return redirect()->route('user.verify')->with('message',$message);
                } 
                else 
                {
                     $message = "Your e-mail is already verified. You can now login.";
                     return redirect()->route('user.verified');
                }
            }
         else
            {
                 return redirect()->route('user.notverify');
            }
        }
        catch(\Exception $exception){
            report($exception);
            return response()->exception();
        }}
        // Forgot Password Method
    public function forgetPassword(ForgotRequest $request)
    {
       try
       {
            $data=$request->validated();
            $User=User::where('email',$data['email']);
           if($User)
            {            
                  $token = Str::random(64); 
                  $link="http://localhost:8000/api/reset/$token";
                 UserVerify::create([
                   'user_id' => $User['id'], 
                     'token' => $token
                  ]);
                Mail::send('resetPassword', ['link' => $link], function($message) use($data){
                $message->to($data['email']);
                 $message->subject('Reset Password link');
                });
            }
        }
        catch(\App\Exceptions\UserNotFoundException $exception){
            report($exception);
            render($exception);
            return response()->exception();
        }
    }
       // Password Reset Method
    public function passwordReset(ResetRequest $request)
    {
        try
        {
            $data=$request->validated();
            if($data)
            {
                 $User = UserVerify::where('token', $data['token'])->first()->update([
                'password'=>Hash::make($data['password']),
                  ]);
                $message = 'Password reset successfully.Now go to login';
                return response()->json($message);
            }
        }
        catch(UserNotFoundException $exception)
        {
            report($exception);
            return response()->exception();
        }catch(\App\Exceptions\UserNotFoundException $exception){
            report($exception);
            render($exception);
            return response()->exception();
        }catch(RouteNotFoundException $exception){
            report($exception);
            return response()->exception();
        }
    }
}