<?php

namespace App\Http\Helpers;

use App\Models\Organizer;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class Common
{
    public function generateToken()
    {
        $token = Str::random(60);
        return $token;
    }

    public function tokenValidation($req, $table)
    {
        $token = $req->bearerToken();
        
        if (!$token) {
            return $this->errorRes('Authentication Failed');
        }

        $user = null;

        switch ($table) {
            case 'organizers':
                $user = Organizer::where('api_token', $token)->first();
                break;

            case 'customers':
                $user = Customer::where('api_token', $token)->first();
                break;

            default:
                return $this->errorRes('Invalid Table');
        }

        if (!$user) {
            return $this->errorRes('Authentication Failed');
        }

        return $user;
    }

    public function errorRes($msg)
    {
        return response()->json([
            'message' => $msg,
            'status' => false
        ], 401);
    }

    public function successRes($msg, $token = null)
    {
        return response()->json([
            'message' => $msg,
            'status' => true,
            'token' => $token
        ], 200);
    }

    public function getImageUrl($imageName, $path)
    {
        $imagePath = $path . '/' . $imageName;
        return asset($imagePath);
    }

    public function storeImage($image, $path, $endName, $extension)
    {
        $imageDirectory = public_path($path);
        
        if (!File::isDirectory($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true, true);
        }

        $imageName = $endName . uniqid() . '.' . $extension;
        $image->move($imageDirectory, $imageName);
        
        return $imageName;
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        $image = $request->file('image');
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->move(public_path('ProfilePic'), $filename);

        $imageUrl = asset('ProfilePic/' . $filename);

        return $imageUrl;
    }
}