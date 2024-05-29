<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

class cloudinaryController extends Controller
{
    public function sendImageToCloudinary($image, string $imageName){

        $validator = Validator::make(
            ['image' => $image],
            ['image' => 'required|image|max:2048']
        );
    
        if ($validator->fails()) {
            throw new \Exception('Invalid image. The image must be a file of type: jpeg, png, bmp, gif, svg, webp and not exceed 2MB.');
        }

        $imageNameWithoutExtension = pathinfo($imageName, PATHINFO_FILENAME);

        $cloudinaryImage = $image->storeOnCloudinaryAs('p3l', $imageNameWithoutExtension);
        $url = $cloudinaryImage->getSecurePath();
        $public_id = $cloudinaryImage->getPublicId();
        
        return $public_id;
    }

    public function deleteImageFromCloudinary(string $imageName){
        if(empty($imageName)){
            throw new \Exception('Image name is required');
        }

        $public_id = $imageName;
        $response = cloudinary()->destroy($public_id);

        return $response;
    }
}