@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Item Returns</h2>

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
                        @if($returns)
                            @foreach($returns as $k => $v)
                            
                                <tr>
                                    <td>{{ $v['order']['orderedByUser']['name'] }}</td>
                                    <td>{!! $v['reason_to_return'] !!}</td>
                                    <td>{{ text_cap_with_replace($v['status']) }}</td>
                                    <td>
                                        <a href="{{route('supply.edit',['pk' => $v['order_id']]) }}" class="btn btn-primary btn-md" >Order details</a>
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