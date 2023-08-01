@extends('academic_theme_1.includes.layout')

@section('css')
@parent

@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            {{-- <h2 class="title">Cancel Order Form</h2>
            
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Vendor and Shop Name</label>
                        <p>{{$order->orderedToUser->name}} | {{$order->orderedToUser->shop_name}}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Order Status</label>
                        <p>{{text_cap_with_replace($order->status)}}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Order Status Description</label>
                        <p>{{$order->status_desc}}</p>
                    </div>

                    <div class="col-md-3">
                        <label for="">Ordered Date</label>
                        <p>{{date_fmt($order->created_at)}}</p>
                    </div>
                </div> --}}

                @if($global_session['role_id'] != Config('constants.CUSTOMER_ROLE_ID'))
                    <h4>Customer Information</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Name and Shop name</label>
                            <p>{{$order->orderedByUser->name}} | {{$order->orderedByUser->shop_name}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Email</label>
                            <p>{{$order->orderedByUser->email}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Customer Type</label>
                            <p>{{text_cap_with_replace($order->orderedByUser->role->name)}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Contact Info</label>
                            <p>{{$order->orderedByUser->profile->contact_no}}</p>
                        </div>

                        @if($global_session['role_id'] == Config('constants.CUSTOMER_ROLE_ID'))
                        <div class="col-md-3">
                            <label for="">Home Address</label>
                            <p>{{$order->orderedByUser->profile->home_address}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Home Location</label>
                            <p>{{$order->orderedByUser->profile->home_location}}</p>
                        </div>
                        @endif

                        @if($global_session['role_id'] != Config('constants.CUSTOMER_ROLE_ID'))
                            <div class="col-md-3">
                                <label for="">Shop Address</label>
                                <p>{{$order->orderedByUser->profile->shop_address}}</p>
                            </div>

                            <div class="col-md-3">
                                <label for="">Shop Location</label>
                                <p>{{$order->orderedByUser->profile->shop_location}}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if($global_session['role_id'] == Config('constants.CUSTOMER_ROLE_ID'))
                <?php //dd($order->orderedToUser->profile); ?>
                    <h4>Retailer Information</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Name and Shop name</label>
                            <p>{{$order->orderedToUser->name}} | {{$order->orderedToUser->shop_name}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Email</label>
                            <p>{{$order->orderedToUser->email}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Customer Type</label>
                            <p>{{text_cap_with_replace($order->orderedToUser->role->name)}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Contact Info</label>
                            <p>{{$order->orderedToUser->profile->contact_no}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Shop Address</label>
                            <p>{{$order->orderedToUser->profile->shop_address}}</p>
                        </div>

                        <div class="col-md-3">
                            <label for="">Shop Location</label>
                            <p>{{$order->orderedToUser->profile->shop_location}}</p>
                        </div>
                    </div>
                @endif


                <h4>Order Items</h4>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tbl" class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Per Price</th>
                                    <th>Gross Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($order->orderItems)
                                    @foreach($order->orderItems as $k => $v)
                                        <tr>
                                            <td>{{ $v['product']['name'] }}</td>
                                            <td>{{ $v['qty'] }}</td>
                                            <td>{{ $v['price_per'] }}</td>
                                            <td>{{ $v['gross_price'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($order["orderedByUser"]["id"]==$global_session['id'])
                    <form action="{{route('order.cancel.store')}}" method="post">
                            @csrf
                            <input type="hidden" name="order_id" value="{{$order['pk']}}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Reason to Cancel Order</label>
                                <textarea name="reason_to_return_or_cancel_order" class="form-control" cols="30" rows="5"></textarea>
                            </div>        
                            <div class="col-md-12 mt-10">
                                <input type="submit" class="btn btn-primary" value="Confirm by clicking on this button">
                            </div>
                            
                        </div>
                    </form>
                @endif

                @if($order["orderedToUser"]["id"]==$global_session['id'])
                    <label for="">Customer reason to return</label>
                    <p>{{$order["reason_to_return"]}}</p>
                    <form action="{{route('order.cancel_or_return.store')}}" method="post">
                            @csrf
                            <input type="hidden" name="order_id" value="{{$order['pk']}}">
                        <div class="row">                        
                            <div class="col-md-12 mt-10">
                                <input type="submit" class="btn btn-primary" value="Accept customer return request by clicking on this button">
                            </div>
                            
                        </div>
                    </form>
                @endif
        </div>
@endsection

@section('scripts')
@parent

@endsection