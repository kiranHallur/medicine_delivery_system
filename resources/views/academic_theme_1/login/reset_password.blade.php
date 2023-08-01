@extends('academic_theme_1.includes.layout')
<style>
</style>
</head>
<body>
    <div  class="container">
        <!-- end header -->
        <form id="reset_form" action="{{route('reset-password-save')}}" method="post" accept-charset="utf-8">
            <?php echo csrf_field(); $required='required="required"'; ?>
            <h2 align="center">Reset Password</h2>
            <div class="form-group col-sm-4 offset-4">               
                <?php $field_name = "user_id"; ?>
                <input type="hidden" class="" name="<?php echo $field_name; ?>" value="<?php echo (!empty($user->id)) ? $user->id : ""; ?>" <?php echo $required; ?> >

                <?php $field_name = "password"; ?>
                <label for="<?php echo $field_name; ?>" class="control-label">New Password <span class="error" >*</span></label>
                <input type="password" class="form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="" <?php echo $required; ?> >
                <?php
                if ($errors->first($field_name)) {
                    ?>
                    <label for="<?php echo $field_name; ?>" class="error"><?php echo $errors->first($field_name); ?></label>
                    <?php
                }
                ?>
            </div>
            <div class="form-group col-sm-4 offset-4">
                <?php $field_name = "confirm_password"; ?>
                <label for="<?php echo $field_name; ?>" class="control-label">Confirm Password <span class="error" >*</span></label>
                <input type="password" class="form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="" <?php echo $required; ?> >
                <?php
                if ($errors->first($field_name)) {
                    ?>
                    <label for="<?php echo $field_name; ?>" class="error"><?php echo $errors->first($field_name); ?></label>
                    <?php
                }
                ?>
            </div>
            <div class="form-group col-sm-4 offset-4">
                <button type="submit" class="btn btn-success">
                    Reset Password
                </button>
            </div>
        </form>   
    </div>
</body>
</html>
