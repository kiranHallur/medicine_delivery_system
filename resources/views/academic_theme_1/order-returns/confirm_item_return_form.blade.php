@extends('academic_theme_1.includes.layout')

@section('css')
@parent

<style>

</style>
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Return Ordered Item Form</h2>
            <div class="row">
                <div class="col-md-3">
                    <label for="">Item Name</label>
                    <input type="text" name="" id="" class="form-control" value="{{$item->product->name}}" disabled >
                </div>

                <div class="col-md-3">
                    <label for="">Ordered Qty</label>
                    <input type="text" name="" id="" class="form-control" value="{{$item->qty}}" disabled >
                </div>

                <div class="col-md-3">
                    <label for="">Price</label>
                    <input type="text" name="" id="" class="form-control" value="{{$item->price}}" disabled >
                </div>                
            </div>

            <hr>
            <h4>Reason for Returning Item</h4>
            <form action="{{route('order.item.return.store')}}" method="post">
                @csrf
                <input type="hidden" name="order_item_id" id="" value="{{$item->order_item_id}}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Reason <span class="required">*</span></label>
                        <textarea type="text" name="reason_to_return" id="reason_to_return" class="form-control" ></textarea>
                    </div>

                    <div class="col-md-3">
                        <input type="submit" class="btn btn-primary btn-md" value="Confirm item to return" >
                    </div>
                </div>
            </form>
        </div>
@endsection

@section('scripts')
@parent
<script src="{{url('public/assets/order-returns/confirm_item_return_form.js')}}"></script>
@endsection