<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\OtpValidation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Spatie\FlareClient\Api;
use Carbon\Carbon;

class RegisterController extends Controller
{
    //

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile_no' => ['required', 'digits:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            return ApiHelper::ApiResponse(true, 422, $validator->errors()->all(), null);
        }
        $request['password'] = Hash::make($request['password']);
        $user = User::create($request->all());
        $token = $user->createToken('appToken')->plainTextToken;
        $user = User::find($user['id']);
        $user->update(['token' => 'Bearer ' . $token]);
        return ApiHelper::ApiResponse(false, 200, "User Register Successfully..", $user);
    }
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'input' => 'required|string',
            'password' => 'nullable|string',
        ]);
        if($validator->fails())
        {
            return ApiHelper::ApiResponse(true,422,$validator->errors()->all(),null);
        }
        $user = User::where(['email' => $request['input']])->orWhere(['mobile_no'=> $request['input']])->get()->first();
        if(is_null($user)){
            return ApiHelper::ApiResponse(true,400,'User does not exist ..!',null);
        }
        if (!is_null($user) && !is_null($request['password'])) {
            $verify= Hash::check($request->password, $user->password);
            if ($verify) {
                $user->tokens()->delete();
                $token = $user->createToken('appToken')->plainTextToken;
                $user = User::find($user['id']);
                $user->update(['token' => 'Bearer ' . $token]);
                return ApiHelper::ApiResponse(false,200,'Login Successfully', $user);
            } else {
               return ApiHelper::ApiResponse(true,400,"Invalid Password ...! ",null);
            }
        } else {
               $otp = rand(100000, 999999);
               $token= md5($request['input'].time());
               $data= OtpValidation::create([
                   'user_id'=>$user->id,
                   'otp'=>$otp,
                   'token'=>$token,
               ]);
               return ApiHelper::ApiResponse(false,200,'Otp Send Successfully ...!', array('token' => $token));
        }
    }

    public function ValidateOtp(Request $request){
        $otp = Validator::make($request->all(), [
            'otp' => 'required|integer|digits:6'
        ]);

        if ($otp->fails()) {
            return ApiHelper::ApiResponse(true,400,"In-valid  OTP ..!");
        }

       if(User::where(['token' => $request['token']])->exists()){

           $valid= OtpValidation::where(['otp' => $request['otp'] ,'is_verify' => false])->where(Carbon::create('created_at')->addMinutes(10),'>=',Carbon::now())->exists();
           $verified= OtpValidation::where(['otp' => $request['otp'] ,'is_verify' => false])->where(Carbon::create('created_at')->addMinutes(10),'>=',Carbon::now())->get()->first();
           $user= user::where(['token' => $request['token']])->get();
           if($valid !== true) {
               return ApiHelper::ApiResponse(true,403, 'otp is invalid or expired, try again',null);
           }
           $verified->update(['is_verify'=> true]);
           return ApiHelper::ApiResponse(false,200,'user Login Successfully ',$user);

       }

        return ApiHelper::ApiResponse(true,400,"Something went Wrong");

    }


}
