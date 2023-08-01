@extends('academic_theme_1.includes.layout')

@section('css')
@parent
<link rel="stylesheet" href="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.css')}}">
@endsection

@section('content')
        <?php $file_name = config('constants.frontend_views').'includes.header'; ?>
        @include($file_name)

        <div class="container">
            <h2 class="title">Users</h2>

            <div class="table-responsive">
                <table id="tbl" class="table table-bordered" style="" >
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($users)
                            @foreach($users as $k => $v)
                                <tr>
                                    <td>{{ $v->name }}</td>
                                    <td>{{ text_cap($v->role->name) }}</td>
                                    <td>@if($v->profile) {{ $v->profile->contact_no }} @else NA @endif</td>
                                    <td>{{ $v->email }}</td>
                                    <td>
                                        <a href="{{route('user.show-info',['pk' => $v['pk']]) }}" class="btn btn-primary btn-sm" >View</a>
                                        <button class="btn btn-sm @if($v->is_deleted==1) btn-success @else btn-danger @endif" id="{{$k}}" onclick="change_status(this, {{$v['pk']}})" > @if($v->is_deleted==1) Unblock @else Block @endif</button>
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
<script src="{{url('public/academic_theme_1/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#tbl').DataTable({ 
            "ordering" : false,
        });
    });


    function change_status(element, id){
        let fd = new FormData();
        fd.append('_token', tokenId);
        fd.append('id', id);
        let res = postFetch("{{route('user.status.store')}}", fd);
        toastr.info("Updating status...");
        res.then((data) => {
            toastr.clear();
            if(data['success']){
                toastr.success(data['msg']);
            }else{
                toastr.error(data['msg']);
            }
        });
    }
</script>
@endsection