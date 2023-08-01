@extends('academic_theme_1.includes.layout')

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)
@endsection