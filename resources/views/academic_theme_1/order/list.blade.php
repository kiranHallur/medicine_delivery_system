@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Orders</h2>

            <div class="table-responsive">
                <table id="tbl" class="table table-bordered" style="" >
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Ordered date</th>
                            <th>Order Satus</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($orders)
                            @foreach($orders as $k => $v)
                                <tr>
                                    <td>{{ $v['ordered_to_user_shop_name'] }}</td>
                                    <td>{{ date('d-m-Y',strtotime($v['created_at'])) }}</td>
                                    <td>{{ text_cap_with_replace($v['status']) }}</td>
                                    <td>
                                        
                                        <a href="{{route('order.edit',['pk' => $v['pk']]) }}" class="btn btn-primary btn-md" >Edit</a>
                                        <a href="{{route('order.cancel.form',['order_id' => $v['pk']]) }}" class="btn btn-danger btn-md" >Cancel Order</a>
                                    </td>
                                </tr>
                            @endforeach
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