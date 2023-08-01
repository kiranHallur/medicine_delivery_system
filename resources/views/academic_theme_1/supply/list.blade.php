@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">

<style>
#tbl td p{
    margin: 0;
}
</style>
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Supply</h2>

            <div class="table-responsive">
                <table id="tbl" class="table table-bordered" style="" >
                    <thead>
                        <tr>
                            <th>
                            <?php
                                if($role_id == config('constants.DEALER_ROLE_ID')){
                                    echo "Retailers";
                                }else if($role_id == config('constants.RETAILER_ROLE_ID')){
                                    echo "Customers";
                                }else if($role_id == config('constants.CUSTOMER_ROLE_ID')){
                                    echo "Retailer";
                                }else{
                                    echo "Customers";
                                }
                            ?>

                            </th>
                            @if($global_session['role_id'] == Config('constants.ADMIN_ROLE_ID'))
                                <th>Ordered To</th>
                            @endif
                            <th>Order Date</th>
                            <th>Status</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($orders)
                            @foreach($orders as $k => $v)
                                <tr>
                                    <td>
                                        @if($v->orderedByUser->role_id == config('constants.CUSTOMER_ROLE_ID'))
                                            {{ text_cap($v->orderedByUser->name) }}
                                        @else
                                            {{ $v->ordered_by_user_shop_name }}
                                        @endif

                                        <hr>

                                        <p>Shop name : <b>{{$v->orderedByUser->profile->shop_name}}</b></p>                               
                                        @if($v->orderedByUser->role_id == config('constants.CUSTOMER_ROLE_ID'))
                                            <p>Address : <b>{{$v->orderedByUser->profile->home_address}}</b></p>
                                            <p>Location : <b>{{$v->orderedByUser->profile->home_location}}</b></p>
                                        @else
                                            <p>Shop Address : <b>{{$v->orderedByUser->profile->shop_address}}</b></p>
                                            <p>Shop Location : <b>{{$v->orderedByUser->profile->shop_location}}</b></p>
                                        @endif
                                    </td>

                                    @if($global_session['role_id'] == Config('constants.ADMIN_ROLE_ID'))
                                        <td>{{ text_cap($v->orderedToUser->name) }} | {{ text_cap($v->orderedToUser->shop_name) }}</td>
                                    @endif

                                    <td>{{ date('d-m-Y',strtotime($v->created_at)) }}</td>
                                    <td>
                                        {{ text_cap_with_replace($v->status) }}
                                        <hr>
                                        {{ $v->status_desc ?? "NA" }}
                                    </td>
                                    <td>
                                        <a href="{{route('supply.edit',['pk' => $v['pk']]) }}" class="btn btn-primary btn-md" >View</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" >You don't have any supplies.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        
        </div>
@endsection

@section('scripts')
@parent
<script src="{{url('public/assets/order/list.js')}}"></script>
<script src="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#tbl').DataTable({
            "ordering" : false,
        });
    });
</script>
@endsection