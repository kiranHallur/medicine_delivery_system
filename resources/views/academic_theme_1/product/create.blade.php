@extends('academic_theme_1.includes.layout')

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">{{ (isset($product['pk'])) ? "Edit" : "Create" }} Product</h2>

            <div class="">

                <?php // dd('ss'); ?>
               <form action="{{ (isset($product['pk'])) ? route('product.update') : route('product.store') }}" method="post" enctype="multipart/form-data" >
                @csrf
                @isset($product['pk'])
                    <input type="hidden" name="product_id" value="{{$product['pk']}}">
                @endisset

                <div class="row ">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php $field_name = "name"; ?>
                            <label for="{{$field_name}}">Name <span class="required">*</span></label>
                            <input type="text" name="{{$field_name}}" id="" class="form-control" value="{!! $product['name'] ?? old($field_name) !!}" required>
                            @if ($errors->first($field_name))
                                <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                            @endif
                        </div>
                        
                        @if($global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                            <div class="form-group">
                                <input type="submit" value="submit" class="btn btn-primary">
                            </div>
                        @endif
                        
                    </div>
                </div>
               </form>
            </div>
        
        </div>
@endsection

@section('scripts')
@parent
<script src="{{url('public/assets/product/create.js')}}"></script>
@endsection