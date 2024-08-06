@extends('admin.admin_dashboard')
@section('content')
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">All Permission</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('add.permission') }}" class="btn btn-primary px-5">Add Permission</a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>S1</th>
                                <th>Permission Name</th>
                                <th>Group Name</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $key => $per)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td> {{ $per->name }} </td>
                                    <td>{{ $per->group_name }}</td>
                                    <td>
                                        <a href="{{ route('edit.permission',$per->id) }}" class="btn btn-info px-5">Edit </a>
                                        <a href="{{ route('delete.permission', $per->id) }}" class="btn btn-danger px-5"
                                            id="delete">Delete </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>




    </div>
@endsection
