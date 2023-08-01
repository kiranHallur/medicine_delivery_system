@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Order Return Request</h2>

            <div class="table-responsive">
                <table id="tbl" class="table table-bordered" style="" >
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Message</th>
                            <th>Order Satus</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($orders)
                            @foreach($orders as $k => $v)
                                @if($v['status']=="RETURN_REQUESTED_BY_CUSTOMER")
                                <tr>
                                    <td>{{ $v['orderedByUser']['name'] }}</td>
                                    <td>{{ $v['reason_to_return'] }}</td>
                                    <td>{{ text_cap_with_replace($v['status']) }}</td>
                                    <td>
                                        <a href="{{route('order.cancel.form',['order_id' => $v['pk']]) }}" class="btn btn-primary btn-md" >View details</a>
                                    </td>
                                </tr>
                                @endif
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