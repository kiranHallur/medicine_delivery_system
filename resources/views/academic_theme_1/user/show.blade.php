@extends('academic_theme_1.includes.layout')

@section('css')
@parent

@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">User : {{text_cap($user->name)}}</h2>
            <hr>
            <h3>Personal Details</h3>
            <div class="row">
                <div class="col-md-3">
                    <label for="">Name</label>
                    <p>{{text_cap($user->name)}}</p>
                </div>

                <div class="col-md-3">
                    <label for="">Email</label>
                    <p>{{$user->email}}</p>
                </div>

                <div class="col-md-3">
                    <label for="">Contact no</label>
                    <p>@if($user->profile) {{ $user->profile->contact_no }} @else NA @endif</p>
                </div>

                <div class="col-md-3">
                    <label for="">Role</label>
                    <p>{{ text_cap($user->role->name) }}</p>
                </div>
                
                @if($user->role_id == config('constants.CUSTOMER_ROLE_ID'))
                    <div class="col-md-3">
                        <label for="">Home Address</label>
                        <p>{{ text_cap($user->profile->home_address) }}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Home Location</label>
                        <p>{{ text_cap($user->profile->home_location) }}</p>
                    </div>
                @endif           

                <div class="col-md-3">
                    <label for="">GST No</label>
                    <p>{{ text_cap($user->profile->gst_no) }}</p>
                </div>
            </div>

            @if($user->role_id != config('constants.CUSTOMER_ROLE_ID') && $user->role_id != config('constants.ADMIN_ROLE_ID'))
                <hr>

                <h3>Shop Details</h3>
                <div class="row"> 

                    <div class="col-md-3">
                        <label for="">Shop Name</label>
                        <p>{{ text_cap($user->profile->shop_name) }}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Shop Address</label>
                        <p>{{ text_cap($user->profile->shop_address) }}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Shop Location</label>
                        <p>{{ text_cap($user->profile->shop_location) }}</p>
                    </div>
                </div>
            @endif
        
        </div>
@endsection

@section('scripts')
@parent
@endsection