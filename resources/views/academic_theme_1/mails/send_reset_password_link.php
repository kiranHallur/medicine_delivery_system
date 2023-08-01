<?php
$project_logo_url = "";
$company_name = config('constants.company_name');

echo View::make('academic_theme_1.mails.includes.css_header', []);
?>
<style>

</style>
<body>
    <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" width="100%" style="max-width:670px;border:1px solid #e8e8e8">
        <tbody>

            <tr>
                <td class="logo">
                    <!-- <div style="float:left;width:100px;" >
                        <img src="<?php echo $project_logo_url; ?>" alt="" style="width:100%;" border="0" class="">
                    </div> -->
                    <div style="padding-left: 15px;" >
                        <p>
                            <?php echo $company_name; ?> 
                        </p>
                    </div>
                    <div style="clear:both"></div>
                </td>
            </tr>

            <tr> 
                <td>
                    <div class="content" >

                        <p class="make_strong" >Hi <?php echo $user->name; ?>,</p>
                        <p>Greetings from <span class="make_strong" ><?php echo $company_name; ?>.</span></p>
                        <p>You're on your way ! Click on below link to reset your password. </p>

                        <a href="<?php echo $reset_link; ?>" target="_blank" >Reset Password</a>

                    </div>
                </td>
            </tr>

            <tr>
                <td bgcolor="#E0E0E0" valign="center" align="center" height="50" style="color:#000000;font:600 13px/18px Segoe UI,Arial">
                    Copyright &#169; <?php echo $company_name; ?>, All rights reserved.
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
