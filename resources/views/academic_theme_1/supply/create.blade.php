@extends('academic_theme_1.includes.layout')

@section('content')
<style>
#user-products-table{
    /* display: none; */
}
</style>
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">{{ (isset($order['pk'])) ? "Edit" : "Create" }} Order</h2>
            <form action="{{ route('order.update') }}" method="post" enctype="multipart/form-data" >
                @csrf
                <div class="">
                    <input type="hidden" name="redirect_route" value="SUPPLY_VIEW">
                    @isset($order['pk'])
                    <input type="hidden" name="order_id" value="{{$order['pk']}}">
                    
                    @endisset

                    <div class="row ">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php $field_name = "ordered_to_user_id"; ?>
                                <label for="{{$field_name}}">Customer <span class="required">*</span></label>
                                <select name="{{$field_name}}" id="{{$field_name}}" class="{{ (isset($order['pk'])) ? "disp_none" : '' }} form-control" onchange="show_user_stock_products(this)" required>
                                    <option value="">Choose</option>
                                    @foreach($users as $k=>$v)
                                        <option value="{{$v['id']}}" <?php echo (isset($order->ordered_to_user_id) && $order->ordered_to_user_id == $v->id)? "selected='selected'" : ""; ?> >{{$v['shop_name']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif

                                @isset($order['pk'])
                                    <input type="hidden" name="order_id" value="{{$order['pk']}}">
                                @endisset     
                                {!! (isset($order['pk'])) ? "<input type='text' class='form-control' value='$order->ordered_to_user_shop_name' disabled>" : '' !!}
                            </div> 
                            
                        </div>


                        @if(config('constants.CUSTOMER_ROLE_ID') != $role_id) 
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php $field_name = "status"; ?>
                                <label for="{{$field_name}}">Status <span class="required">*</span></label>
                                <select name="{{$field_name}}" id="{{$field_name}}" class="form-control" onchange="show_user_stock_products(this)" required>
                                    <option value="">Choose</option>
                                    @foreach($status_choices as $k=>$v)
                                        <option value="{{$v['id']}}" <?php echo (isset($order->status) && $order->status == $v["id"])? "selected='selected'" : ""; ?> >{{$v['title']}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif
                            </div>                        
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?php $field_name = "status_desc"; ?>
                                <label for="{{$field_name}}">Status Description<span class="required">*</span></label>
                                <textarea name="{{$field_name}}" class="form-control">{{$order->status_desc}}</textarea>
                            </div>
                        </div>
                        @endif
 
                        @if($global_session['role_id'] != Config('constants.ADMIN_ROLE_ID') && $global_session['role_id'] != Config('constants.CUSTOMER_ROLE_ID'))
                            <div class="col-md-3 flex_bottom">
                                <div class="form-group">
                                    <input type="submit" value="submit" class="btn btn-primary btn-md">
                                </div>                        
                            </div>                            
                        @endif

                    </div>

                    <div class="row disp_none">
                        <div class="col-md-12">                    
                            <h3 class="title">Products</h3>
                            <div class="table-responsive">
                                <table id="user-products-table" class="table table-bordered" style="" >
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Price per</th>
                                            <th>Stock Qty</th>
                                            <th>Purchase Qty</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="table-responsive">
                    <table id="items-table" class="table table-bordered" style="" >
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Purchase Qty</th>
                                <th>Price per</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total : </td>
                                <td id="total_qty" ></td>
                                <td  ></td>
                                <td id="total_amount" ></td>
                                <td ></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>
@endsection

@section('scripts')
@parent
<script src="{{url('public/assets/supply/create.js')}}"></script>
<script>
    <?php
    // dd($order->stockItems);
    if(!empty($order->orderItems)){
        foreach($order->orderItems as $k => $v){ 
            ?>
            obj = JSON.parse('<?php echo json_encode($v); ?>');
            console.log(obj);
            items_container['items'].push(new Item(obj));
            <?php 
        }
    } 
    ?>
    console.log(items_container['items']);

    if(items_container['items'].length){
        table_render();
    }

    window.addEventListener('load', function(event){
        if(ordered_to_user_id_element.value!=""){
            show_user_stock_products(ordered_to_user_id_element);
        }
    });
</script>

@endsection