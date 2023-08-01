@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Products</h2>

            <div class="table-responsive">
                <table id="tbl" class="table table-bordered" style="width: 55%;" >
                    <thead>
                        <tr>
                            <th>Name</th>
                            @if($global_session['role_id'] == Config('constants.ADMIN_ROLE_ID'))
                                <th>Added by User</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($products))
                            @foreach($products as $k => $v)
                                <tr>
                                    <td>{!! $v['name'] !!}</td>
                                    @if($global_session['role_id'] == Config('constants.ADMIN_ROLE_ID'))
                                        <td>{{ $v->addedByUser->name }}</td>
                                    @endif
                                    <td>
                                        <a href="{{route('product.edit',['pk' => $v['pk']]) }}" class="btn btn-primary btn-md" >
                                            @if($global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                                                Edit
                                            @else
                                                View
                                            @endif
                                        </a>
                                        @if($global_session['role_id'] != Config('constants.ADMIN_ROLE_ID'))
                                            <button type="button" class="btn btn-danger btn-md" onclick="remove(this, {{$v['pk']}})" >Remove</button>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center" >You have no products.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        
        </div>
@endsection

@section('scripts')
@parent
<script src="{{url('public/assets/product/list.js')}}"></script>
<script src="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#tbl').DataTable({
            "ordering" : false,
        });
    });
</script>
@endsection