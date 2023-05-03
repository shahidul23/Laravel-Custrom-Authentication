<?php

namespace App\Http\Controllers;
use App\Models\User;
use Hash;
use Session;

use Illuminate\Http\Request;

class CustomAuthController extends Controller
{
    public function login(){
        return view("auth.login");
    }
    public function registration(){
        return view("auth.registration");
    }
    public function registerUser(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8|max:30'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email =$request->email;
        $user->password = Hash::make($request->password);

        $result = $user->save();
        if($result){
            return back()->with('success','You have registered successfully');
        }else{
            return back()->with('fail','Something wrong');
        }

    }
    public function loginUser(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:8|max:30'
        ]);
        $user = User::where('email','=',$request->email)->first();
        if($user){
            if(Hash::check($request->password, $user->password)){
                $request->session()->put('LoginId',$user->id);
                return redirect('dashboard');
            }else{
                return back()->with('fail','This Password not matches !!!');
            }
        }else{
            return back()->with('fail','This email is not registered !!!');
        }

    }
    public function dashboard(){
        $data = array();
        if(Session::has('LoginId')){
            $data = User::where('id','=',Session::get('LoginId'))->first();
        }
        return view("auth.dashboard",compact('data'));
    }
    public function logout(){
        if(Session::has('LoginId')){
            Session::pull('LoginId');
            return redirect('login');
        }
    }
}