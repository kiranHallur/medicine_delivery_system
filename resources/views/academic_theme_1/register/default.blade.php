@extends('academic_theme_1.includes.layout')
<style>
.defualt-wrapper > div{
    display: flex;
    justify-content: center;
}

.defualt-wrapper > div > a{
    margin: 5px;
}
</style>
<?php // dd($errors); ?>
<div class="container jumbotron m-t-15">
    <h2 class="title text-center text-capitalize">Register as {{$role}}</h2>
    <form action="{{route('register.store')}}" method="post">
        @csrf
        <input type="hidden" name="role_id" id="role_id" value="{{ $role_id }}">

        <div class="row">
            <div class="col-md-4 offset-4 ">
                
                <div class="form-group">
                    <?php $field_name="name"; ?> 
                    <label for="<?php echo $field_name; ?>">Name <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>
            
                <div class="form-group">
                    <?php $field_name="username"; ?>
                    <label for="<?php echo $field_name; ?>">Username <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>

                <div class="form-group">
                    <?php $field_name="email"; ?>
                    <label for="<?php echo $field_name; ?>">Email <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>

                <div class="form-group">
                    <?php $field_name="password"; ?>
                    <label for="<?php echo $field_name; ?>">Password <span class="required">*</span></label>
                    <input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>

                <div class="form-group">
                    <?php $field_name="confirm_password"; ?>
                    <label for="<?php echo $field_name; ?>">Confirm Password <span class="required">*</span></label>
                    <input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>

                <hr>

                @if(config('constants.CUSTOMER_ROLE_ID') != $role_id)
                <div class="form-group">
                    <?php $field_name="shop_name"; ?> 
                    <label for="<?php echo $field_name; ?>">Shop Name <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>
                @endif

                @if(config('constants.CUSTOMER_ROLE_ID') != $role_id)
                <div class="form-group">
                    <?php $field_name="shop_address"; ?>
                    <label for="<?php echo $field_name; ?>">Shop Address </label>
                    <textarea type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" >{{ old($field_name) }}</textarea>
                </div>
                @endif

                @if(config('constants.CUSTOMER_ROLE_ID') == $role_id)
                    <div class="form-group">
                        <?php $field_name="home_address"; ?>
                        <label for="<?php echo $field_name; ?>">Address </label>
                        <textarea type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" >{{ old($field_name) }}</textarea>
                    </div>
                @endif


                @if(config('constants.CUSTOMER_ROLE_ID') != $role_id)
                <div class="form-group">
                    <?php $field_name="gst_no"; ?>
                    <label for="<?php echo $field_name; ?>">GST No </label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}">
                </div>
                
                <div class="form-group">
                    <?php $field_name="shop_location"; ?>
                    <label for="<?php echo $field_name; ?>">Shop Location <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>
                @endif


                @if(config('constants.CUSTOMER_ROLE_ID') == $role_id)
                <div class="form-group">
                    <?php $field_name="home_location"; ?>
                    <label for="<?php echo $field_name; ?>">Location <span class="required">*</span></label>
                    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}" required>
                    @if ($errors->first($field_name))
                        <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                    @endif
                </div>
                @endif

                <div class="form-group">
                    <?php $field_name="contact_no"; ?>
                    <label for="<?php echo $field_name; ?>">Contact no</label>
                    <input type="number" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="form-control" value="{{ old($field_name) }}">
                </div>
            

                <div class="form-group">
                    <input type="submit" value="Register" class="btn btn-primary">
                </div>
            </div>
        </div>
    </form>
</div>