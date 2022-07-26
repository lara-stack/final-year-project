<?php

namespace App\Http\Controllers\Admin\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Mail\OTP;
use Hash;
use Session;
use DB;
use Config;
class ForgotController extends Controller
{
    //
    public function forgot_password_view(Request $request){
        $request->session()->forget('email'); 
        return view('admin.login.vw_forgot_password');
    }

    public function send_otp(Request $request){
        $validator = $request->validate([
            'email' => 'required|email'
        ]);
        if($validator){
            $check_email = $request->email;
            $verified_email = Admin::where('email','=',$check_email)->first();  
       
            if($verified_email){
                $otp = rand(1111,9999);
                $save_otp = Admin::where('email','=',$check_email)->update(['otp'=>$otp]);
                \Mail::to($check_email)->send(new OTP($otp));
                $request->session()->put('email',$check_email);
                return redirect('/check-otp')->with('message', 'OTP send Successfully!');
            }                
        }         
        return redirect('forgot-password')->with('error', 'Enter valid email.');
    }


    public function otp_verify(Request $request){
        $validator = $request->validate([
            'otp' => 'required|numeric|digits:4',
        ]);

        if($validator){
            $check_otp = Admin::where('otp','=',$request->otp)->value('otp');
            if($check_otp){
                return redirect('reset-password');
            }
            else{
                $request->session()->forget('email'); 
                return redirect()->back();
            }
        }
        $request->session()->forget('email'); 
    }

    public function reset_password_view(){
        $email = session('email');
        if(empty($email)){
            return redirect('/');
        }
        return view('admin.login.reset-password');
    }

    public function reset_password(Request $request){
        $email = session('email'); 
        $validator = $request->validate([
            'new_password' => 'Required|min:6',
            'confirm_password' => 'Required|same:new_password|min:6'
        ]);
    
        if($validator){   
            $new_password = $request->new_password;
            $confirm_password = $request->confirm_password;   
            if($new_password == $confirm_password){
                $password = Hash::make($confirm_password);
                Admin::where('email',$email)->update(['password'=> $password]);
                Session::flush();
                return redirect('/')->with('message', 'Password Change Successfully!');;
            }
            else{
                return redirect()->back();
            }
        } 
        else{
            return redirect()->back();
        }   
    }
}
