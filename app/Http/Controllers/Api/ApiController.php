<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    //Register Api, Logon  In Api, Profile Api, Logout Api
    //Post [Name,Email,Password]

    public function register(Request $request)
    {
        //http://sanctum-api.test/api/register

        //First defined Validation data ko pass karne k bad user
        $request->validate([
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|confirmed"

        ]);
        // dd($request->all());

        //user

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        return response()->json([
            "status" => true,
            "message" => "User registered Successfully",
            "data" => []
        ]);
    }


    //Login email and password post req


    public function login(Request $request)

    //http://sanctum-api.test/api/login
    {
        //Validation
        $request->validate([
            "email" => "required|email|string",
            "password" => "required"
        ]);
// dd($request->email);
        //Email Check
        $user = User::where("email" , $request->email)->first();
        // dd($user->password);
        if(!empty($user)){
            //users exits
            if(Hash::check($request->password,$user->password)){

                //Password matched

                $token = $user->createToken("myToken")->plainTextToken;
                return response()->json([
                    "status" => true,
                    "message" => "You are login In Successfully",
                    "token" => $token,
                    "data" => []
                ]);


            } else{

                return response()->json([
                    "status" => false,
                    "message" => "Invalid Password",
                    "data" => []
                ]);

            }


        } else{
            return response()->json([
                "status" => false,
                "message" => "Emsil doesn't match with records ",
                "data" => []
            ]);
        }
    }


    //Profile  Get req Auth Token

    public function profile()

    //http://sanctum-api.test/api/profile
    {
        $userData = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Profile Information",
            "data" => $userData,
            "id" => auth()->user()->id,

        ]);
    }

    //logout get req Auth Token

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => true,
            "message" => "User logged out",
            "data" =>[]
        ]);
    }
}
