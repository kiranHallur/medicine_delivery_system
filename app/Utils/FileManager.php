<?php

namespace App\Utils;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FileManager extends Controller {

    public $allowed_mimes = ['image/png', 'image/jpeg', 'image/gif'];

    public function __construct() {
        parent::__construct();
    }

    public function fileUpload($file_object, $options) {
        $info = [
            'success' => FALSE,
            'file_name' => NULL,
        ];
        try {
            if ($this->isValidMimeType($file_object)) {
                $path = $options['path'];
                // if (!file_exists($path)) {
                //    mkdir($path, 0777, true);
                // }

                // dd($file_object);
                $mime_type = $this->getExtension($file_object);
                $file_name = date('d-m-Y__h-i-s__A') . "__" . rand(0, 100000) . "." . $mime_type;
                $file = $file_object->storeAs($path, $file_name, 'public_uploads');
                // dd($file);
                $info['success'] = TRUE;
                $info['file_name'] = $file_name;
            }
        } catch (Exception $ex) {
            Log::info("FILE UPLOAD ERROR : \n".json_encode($ex));
        }
        return $info;
    }

    public function isValidMimeType($file_object) {
        $data = @getimagesize($file_object);
        if (empty($data)) {
            return FALSE;
        }
        if (in_array($data['mime'], $this->allowed_mimes)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getMime($file_object) {
        $data = @getimagesize($file_object);
        $mime = (!empty($data['mime'])) ? $data['mime'] : NULL;
        return $mime;
    }

    public function getExtension($file_object) {
        $data = $this->getMime($file_object);
        $mime = (!empty($data)) ? explode('/', $data)[1] : NULL;
        return $mime;
    }

    public function sanitizeFile() {
        
    }
    

}