@extends('academic_theme_1.includes.layout')
<style>
    #forgot-password-div{
        display: none;
    }
</style>
</head>
<body>
    <div class="container">
        <!-- end header -->
        <form action="{{route('email-reset-password-link')}}" method="post" accept-charset="utf-8">
            <?php echo csrf_field(); ?>
            <h2 align="center">Forgot Password</h2>
            <div class="form-group col-sm-4 offset-4">
                <?php $field_name="email"; ?>
                <label for="<?php echo $field_name; ?>" class="control-label">Email<span class="error" >*</span></label>
                <input type="text" required class="form-control" name="<?php echo $field_name; ?>" value="<?php echo (!empty($member_id))? $member_id : old($field_name); ?>" required="required" >
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
                    <?php
                    if(!empty(Session::get("command")) && Session::get("command") == "SHOW_RESEND_BTN"){
                        echo "Resend Password Reset Link";
                    }else{
                        echo "Send Password Reset Link";
                    }
                    ?>
                </button>
            </div>
        </form>   
    </div>


</body>
</html>
