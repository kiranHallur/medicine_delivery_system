<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Mail\MailInterface;
use Mail;
use DB;

class MailHelper extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function send($data) {
        // $data['to'] = "pavanbaddi911@gmail.com";
        $is_sent = FALSE;
        try {
            $cc = (!empty($data['cc'])) ? $data['cc'] : [];
            $bcc = (!empty($data['bcc'])) ? $data['bcc'] : [];
            // echo (new MailInterface($data))->render();
            // dd($data);
            $mail = Mail::to($data['to'])->cc($cc)->bcc($bcc)->send(new MailInterface($data));
            // dd($mail);
            $is_sent = TRUE;
        } catch (Exception $ex) {
            // dd($ex);
            Log::info("ERROR MailHelper->send() : " . json_encode($ex) . "\n\n data = \n" . json_encode($data));
        }
        return $is_sent;
    }

}
