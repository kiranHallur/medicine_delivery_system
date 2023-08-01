@extends('academic_theme_1.includes.layout')

@section('content')
    <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
    @include($file_name)

    <div class="container">
        <div style="margin-top:15px;" >
            <!-- Nav pills -->
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="pill" href="#profile_tab">Profile</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#change_password_tab">Change Password</a>
                </li>

                @if($global_session['role_id'] != Config('constants.CUSTOMER_ROLE_ID') && $global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#shop_tab">Shop</a>
                    </li>
                @endif
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane container active" id="profile_tab">
                    <form action="{{ route('user.profile.update') }}" method="post" enctype="multipart/form-data" >
                        <div class="row">
                            <div class="col-md-3">
                                <?php $field_name = "username"; ?>
                                <label for="<?php echo $field_name; ?>">Username <span class="required">*</span></label>
                                <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user['username'] ?? old($field_name) }}" disabled>
                            </div>

                            <div class="col-md-3">
                                <?php $field_name = "name"; ?>
                                <label for="<?php echo $field_name; ?>">Name <span class="required">*</span></label>
                                <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user[$field_name] ?? old($field_name) }}" required>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <?php $field_name = "email"; ?>
                                <label for="<?php echo $field_name; ?>">Email <span class="required">*</span></label>
                                <input type="email" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user[$field_name] ?? old($field_name) }}" required>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <?php $field_name = "gst_no"; ?>
                                <label for="<?php echo $field_name; ?>">GST No</label>
                                <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user['profile'][$field_name] ?? old($field_name) }}" >
                            </div>

                            @if($global_session['role_id'] == Config('constants.CUSTOMER_ROLE_ID'))
                                <div class="col-md-3">
                                    <?php $field_name = "home_location"; ?>
                                    <label for="<?php echo $field_name; ?>">Home Location</label>
                                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user['profile'][$field_name] ?? old($field_name) }}" >
                                </div>

                                <div class="col-md-3">
                                    <?php $field_name = "home_address"; ?>
                                    <label for="<?php echo $field_name; ?>">Home Address</label>
                                    <textarea type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" >{{ $user['profile'][$field_name] ?? old($field_name) }}</textarea>
                                </div>
                            @endif

                            <div class="col-md-12" style="margin-top:15px;" >
                                <input type="submit" class="btn-primary btn-md btn" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane container" id="change_password_tab">
                    <form action="{{ route('user.change-password.update') }}" method="post">
                            <div class="row mt-10">
                                <div class="col-md-3">
                                    <?php $field_name = "old_password"; ?>
                                    <label for="<?php echo $field_name; ?>">Old Password <span class="required">*</span></label>
                                    <input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="" required>
                                    @if ($errors->first($field_name))
                                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <?php $field_name = "new_password"; ?>
                                    <label for="<?php echo $field_name; ?>">New Password <span class="required">*</span></label>
                                    <input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="" required>
                                    @if ($errors->first($field_name))
                                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                    @endif
                                </div>
                            </div>
                            

                            <div class="row">
                                <div class="col-md-3">
                                    <?php $field_name = "confirm_password"; ?>
                                    <label for="<?php echo $field_name; ?>">Confirm Password <span class="required">*</span></label>
                                    <input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="" required>
                                    @if ($errors->first($field_name))
                                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12" style="margin-top:15px;" >
                                    <input type="submit" class="btn-primary btn-md btn" value="Submit">
                                </div>
                            </div> 
                    </form>
                </div>

                @if($global_session['role_id'] != Config('constants.CUSTOMER_ROLE_ID') && $global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                    <div class="tab-pane container" id="shop_tab">
                        <form action="{{ route('user.shop.update') }}" method="post">
                                <div class="row mt-10">
                                    <div class="col-md-3">
                                        <?php $field_name = "shop_name"; ?>
                                        <label for="<?php echo $field_name; ?>">Shop Name <span class="required">*</span></label>
                                        <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user['profile'][$field_name] ?? old($field_name) }}" required>
                                        @if ($errors->first($field_name))
                                            <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <?php $field_name = "shop_address"; ?>
                                        <label for="<?php echo $field_name; ?>">Shop Address</label>
                                        <textarea type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" >{{ $user['profile'][$field_name] ?? old($field_name) }}</textarea>
                                    </div>

                                    <div class="col-md-3">
                                        <?php $field_name = "shop_location"; ?>
                                        <label for="<?php echo $field_name; ?>">Shop Location <span class="required">*</span></label>
                                        <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ $user['profile'][$field_name] ?? old($field_name) }}" required>
                                        @if ($errors->first($field_name))
                                            <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12" style="margin-top:15px;" >
                                        <input type="submit" class="btn-primary btn-md btn" value="Submit">
                                    </div>
                                </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent

@endsection