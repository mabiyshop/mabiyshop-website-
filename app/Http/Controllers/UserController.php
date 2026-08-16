<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class UserController extends Controller{
	
	
    public function signup(Request $request){
		$user_id = Auth::id();
		if($user_id){
			return response()->json('You are loged in.', 200);
		}
        $this->validate($request, [
            'first_name' => 'required',
			'last_name' => 'required',
			'phone' => 'required|unique:users,phone',
            'email' => 'required|unique:users,email',
			'password' => 'required|min:6',
			'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);
		$data['firstname']= $request->first_name;
		$data['lastname']= $request->last_name;
		$data['phone']     = $request->phone;
		$data['email']     = $request->email;
		$data['password']  = Hash::make($request->password);
		$data['status']    = 1;
		DB::table('users')->insert($data);
        return response()->json('Singup successfull.', 200);
    }   


    public function signin(Request $request){
		if(Auth::check()){
			return redirect('/my-account');
		}
		
		$this->validate($request, [
			'phone' => 'required',
			'password' => 'required',
        ]);
		

		
		if(Auth::attempt(array('phone'=>$request->phone, 'password'=> $request->password))){
			return response()->json(Auth::user(), 200);
		}else{
			return response()->json(2, 400);
		}
    } 
	
	
    public function logout(){
		if(Auth::check()){
			Auth::logout();
			return response()->json(1, 200);
		}else{
			return response()->json(2, 200);
		}
    } 
	
    public function getuser(){
		if(Auth::check()){
			return response()->json(Auth::user());
		}else{
			return response()->json(null, 200);
		}
    }	
    public function checkuser(){
		if(Auth::check()){
			return response()->json(1, 200);
		}else{
			return response()->json(2, 200);
		}
    }
	
	

	

	
	
	
}
