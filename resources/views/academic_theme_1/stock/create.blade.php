@extends('academic_theme_1.includes.layout')

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">{{ (isset($stock['pk'])) ? "Edit" : "Create" }} stock</h2>
            <form action="{{ (isset($stock['pk'])) ? route('stock.update') : route('stock.store') }}" method="post" enctype="multipart/form-data" >
                @csrf
                <div class="">
                
                    @isset($stock['pk'])
                        <input type="hidden" name="stock_id" value="{{$stock['pk']}}">
                    @endisset

                    <div class="row ">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php $field_name = "supplier_name"; ?>
                                <label for="{{$field_name}}">Supplier Name <span class="required">*</span></label>
                                <input type="text" name="{{$field_name}}" id="" class="form-control" value="{!! $stock[$field_name] ?? old($field_name) !!}" required>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <?php $field_name = "supplier_invoice_date"; ?>
                                <label for="{{$field_name}}">Supplier Date <span class="required">*</span></label>
                                <input type="date" name="{{$field_name}}" id="" class="form-control" value="{{$stock[$field_name] ?? old($field_name) }}" required>
                                @if ($errors->first($field_name))
                                    <label for="{{$field_name}}" class="error">{{$errors->first($field_name)}}</label>
                                @endif
                            </div>
                        </div>
 
                        @if($global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                            <div class="col-md-3 flex_bottom">
                                <div class="form-group">
                                    <input type="submit" value="submit" class="btn btn-primary btn-md">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row m-t-15">

                        <div class="col-md-3">
                            <div class="form-group">

                                <input type="hidden" id="stock_item_id" value="">
                                <input type="hidden" id="index_field" value="">

                                <?php $field_name = "product_id"; ?>
                                <label for="{{$field_name}}">Product <span class="required">*</span></label>
                                <select id="{{$field_name}}" class="form-control">
                                    <option value="">Choose</option>
                                    @foreach($products as $k => $v)
                                        <option value="{{$v['pk']}}">{{$v['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <?php $field_name = "barcode"; ?>
                                <label for="{{$field_name}}">Barcode</label>
                                <input type="text" id="{{$field_name}}" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <?php $field_name = "price_per"; ?>
                                <label for="{{$field_name}}">Price Per</label>
                                <input type="number" id="{{$field_name}}" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <?php $field_name = "initial_qty"; ?>
                                <label for="{{$field_name}}">Initial Qty</label>
                                <input type="text" min="0" id="{{$field_name}}" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <?php $field_name = "stock_qty"; ?>
                                <label for="{{$field_name}}">Stock Qty</label>
                                <input type="text" min="0" id="{{$field_name}}" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group pos_center" >
                                <?php $field_name = "is_closed"; ?>
                                <label for="{{$field_name}}">Is Stock Closed</label> &nbsp; &nbsp;
                                <input type="checkbox" min="0" id="{{$field_name}}" class="" value="">
                            </div>
                        </div>
  
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="button" onclick="push_item()" value="Add Item" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="table-responsive">
                    <table id="items-table" class="table table-bordered" style="" >
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Barcode</th>
                                <th>Price Per</th>
                                <th>Initial Qty</th>
                                <th>Stock Qty</th>
                                <th>Is Closed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </form>
        </div>
@endsection 

@section('scripts')
@parent
<script src="{{url('public/assets/stock/create.js')}}"></script>
<script>
    <?php
    // dd($stock->stockItems);
    if(!empty($stock->stockItems)){
        foreach($stock->stockItems as $k => $v){
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
</script>

@endsection