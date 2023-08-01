@extends('academic_theme_1.includes.layout')

@section('content')
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
    <h2 class="title text-center">Login As</h2>
    <div class="row defualt-wrapper">
        <div class="col-md-6 offset-3 ">
            <a href="{{route('login')}}?role=dealer" class="btn btn-primary" >Dealer</a>
            <a href="{{route('login')}}?role=retailer" class="btn btn-success" >Retailer</a>
            <a href="{{route('login')}}?role=customer" class="btn btn-info" >Customer</a>
            <a href="{{route('login')}}?role=admin" class="btn btn-warning" >Admin</a>
        </div>
    </div>

    <hr>

    <h2 class="title text-center">Register As</h2>
    <div class="row defualt-wrapper">
        <div class="col-md-6 offset-3 ">
            <a href="{{route('register')}}?role=dealer" class="btn btn-primary" >Dealer</a>
            <a href="{{route('register')}}?role=retailer" class="btn btn-success" >Retailer</a>
            <a href="{{route('register')}}?role=customer" class="btn btn-info" >Customer</a>
        </div>
    </div>
</div>
@endsection