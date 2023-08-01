@extends('base')

@section('title', config('constants.company_name'))

<?php 
$session = Session::get(config('constants.session_name'));
$access_token = (isset($session["access_token"]))? $session["token_type"]." ".$session["access_token"] : "";
?>

@section('css')
<?php echo View::make(config('constants.frontend_views').'includes.css_header', []); ?>
@endsection

@section('style')
@endsection

@section('content')
 
@endsection


@section('footer')
<?php echo View::make(config('constants.frontend_views').'includes.footer', []); ?>
@endsection

@section('js')
<input type="hidden" id="tokenId" value="<?php echo csrf_token(); ?>">
<input type="hidden" id="base_url" value="<?php echo url(''); ?>">
<script>
let tokenId = document.getElementById('tokenId').value;
let api_url = document.getElementById('base_url').value+'/';
let headers = {
    // 'Accept' : 'application/json',
    'Authorization' : "{{$access_token}}",
}

let current_user_role_id = '<?php echo $global_session['role_id'] ?? ''; ?>';
let app_constants = {
    "DEALER_ROLE_ID" : '<?php echo Config('constants.DEALER_ROLE_ID'); ?>',
    "RETAILER_ROLE_ID" : '<?php echo Config('constants.RETAILER_ROLE_ID'); ?>',
    "CUSTOMER_ROLE_ID" : '<?php echo Config('constants.CUSTOMER_ROLE_ID'); ?>',
    "ADMIN_ROLE_ID" : '<?php echo Config('constants.ADMIN_ROLE_ID'); ?>',
};

</script>
@endsection

@section('scripts')
@parent
<script src="{{url(config('constants.frontend_theme').'js/jquery.min.js')}}"></script>
 <script src="{{url(config('constants.frontend_theme').'js/popper.min.js')}}"></script>
<script src="{{url(config('constants.frontend_theme').'js/bootstrap.min.js')}}"></script>
<script src="{{url(config('constants.frontend_theme').'plugins/toastr/toastr.min.js')}}"></script>
<script src="{{url('public/assets/common.js')}}"></script>

<script>
    $(document).ready(function () {
        let msg = '<?php echo Session::get("success"); ?>';
        if (msg != "") {
            toastr.success(msg);
        }
        let error = '<?php echo Session::get("error"); ?>';
        if (error != "") {
            toastr.error(error);
        }
        let warning = '<?php echo Session::get("warning"); ?>';
        if (warning != "") {
            toastr.warning(warning);
        }
    });
</script>

@endsection
