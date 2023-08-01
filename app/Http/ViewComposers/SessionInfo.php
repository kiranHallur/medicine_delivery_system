<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use App\Models\CompanySettingModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SessionInfo {

    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $session_info;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct() {
        $user =session(Config('constants.session_name'));
        $this->session_info= (isset($user['user']) && !empty($user['user']))? $user['user'] : [];
        // dd($this->session_info);
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view) {
        $view->with('global_session', $this->session_info);
    }

}
