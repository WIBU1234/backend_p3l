<?php

namespace App\Http\Controllers;

class cloudinaryController extends Controller
{
    public function sendImageToCloudinary($image, string $imageName){
        $cloudinaryImage = $image->storeOnCloudinaryAs('p3l', $imageName);
        $url = $cloudinaryImage->getSecurePath();
        $public_id = $cloudinaryImage->getPublicId();
        
        return $public_id;
    }

    public function deleteImageFromCloudinary(string $imageName){
        $public_id = $imageName;
        $response = cloudinary()->destroy($public_id);

        return $response;
    }
}