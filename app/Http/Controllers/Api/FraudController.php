<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Models\fraud;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\FlareClient\Api;

class FraudController extends Controller
{
    public function FraudRegistration(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required|string',
            'fraud' => 'required|string',
            'address' => 'required',
            'mobile_number' => 'required|digits:10',
            'attach_file' => 'nullable|file'
        ]);
        if ($validate->fails()){
            return ApiHelper::ApiResponse(true, 400, $validate->errors()->all(), null);
        }

        $freg = fraud::create([
            'id' => auth()->user()->id,
            'name' => $request['name'],
            'fraud_type' => $request['fraud'],
            'address' => $request['address'],
            'mobile_number' => $request['mobile_number'],
        ]);

        if ($freg){
            return ApiHelper::ApiResponse(false,400, "Data inserted successfully",null);
        }
    }

    public function ViewFraudRequest($id) {
        $id = htmlspecialchars(stripslashes($id));
        $view_req = fraud::find($id);
        if (is_null($view_req)){
            return ApiHelper::ApiResponse(true,400,'No Record Found..!',$view_req);
        }
        return ApiHelper::ApiResponse(false,200,'Date fetch successfully ',$view_req);
    }

    public function UpdateFraudRequest(Request $request, $id){
       $validate = Validator::make($request->all(), [
           'name' => 'required|string',
           'fraud' => 'required|string',
           'address' => 'required',
           'mobile_number' => 'required|digits:10',
           'attach_file' => 'nullable|file'
       ]);
       $fr_check = fraud::find($id);
       $update = $fr_check->update($request->all());
       if (is_null($update)){
        return ApiHelper::ApiResponse(true, 400, 'Failed To Update',$update);
       }
       return ApiHelper::ApiResponse(false, 200, 'Successfully Updated', $update);
    }

    public function DeleteFraudRequest($id){
        $fr_del = fraud::find($id);
        $delete = $fr_del->delete();
        if ($delete !== true){
            return ApiHelper::ApiResponse(true, 400, 'Failed To Delete', $delete);
        }
        return ApiHelper::ApiResponse(false, 200, 'Deleted Successfully', $delete);
    }
}
