<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\CompanySettingModel;
use Illuminate\Support\Facades\Log;

class MailInterface extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $view_path = NULL;
    protected $data = NULL;

    public function __construct($info) {
        $this->view_path = $info['view_path'];
        $this->data = $info;
    }

    public function build() {
        $mail = $this->from(config('constants.admin_mail'), config('constants.company_name'))
                ->subject($this->data['subject'])
                ->view($this->view_path);
        $mail = $mail->with($this->data);
        // dd($mail);
        Log::info("Sending Mail details : \n\n".json_encode($this->data));    
        return $mail;
    }
}
