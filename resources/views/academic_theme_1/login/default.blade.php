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

<div class="container jumbotron m-t-15">
    <h2 class="title text-center text-capitalize">{{$role}} Login</h2>
    <form action="{{route('login.verify')}}" method="post">
        @csrf
        <input type="hidden" name="role_id" id="" value="{{$role_id}}"  required>
        <div class="row">
            <div class="col-md-4 offset-4 ">
                <div class="form-group">
                    <label for="">Username <span class="required">*</span></label>
                    <input type="text" name="username" id="" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="">Password <span class="required">*</span></label>
                    <input type="password" name="password" id="" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="login" class="btn btn-primary">
                    <a href="{{route('forgot-password')}}" >Forgot Password ?</a>
                </div>
            </div>
        </div>
    </form>
</div>