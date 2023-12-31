@extends('admin/components/layout')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Table User</h1>
    

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">DataTables User</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td>Tiger Nixon</td>
                                <td>System Architect</td>
                                <td>Edinburgh</td>
                                <td>61</td>
                                <td>2011/04/25</td>
                                <td>$320,800</td>
                                <td>
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal{{$i}}"><i class="fas fa-fw fa-edit"></i>Edit</button>
                                    <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$i}}"><i class="fas fa-fw fa-trash"></i>Hapus</button>
                                    
                                </td>
                            </tr>
                            <!-- Modal for Edit -->
                            <div class="modal fade" id="editModal{{$i}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{$i}}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <!-- Your modal content for edit goes here -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="form-group">
                                                    <label for="name{{$i}}">Name</label>
                                                    <input type="text" class="form-control" id="name{{$i}}" placeholder="Enter name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email{{$i}}">Email</label>
                                                    <input type="email" class="form-control" id="email{{$i}}" placeholder="Enter email">
                                                </div>
                                                <div class="form-group">
                                                    <label for="password{{$i}}">Password</label>
                                                    <input type="password" class="form-control" id="password{{$i}}" placeholder="Enter password">
                                                </div>
                                                <div class="form-group">
                                                    <label for="age{{$i}}">Age</label>
                                                    <input type="number" class="form-control" id="age{{$i}}" placeholder="Enter age">
                                                </div>
                                                <div class="form-group">
                                                    <label for="address{{$i}}">Address</label>
                                                    <textarea class="form-control" id="address{{$i}}" placeholder="Enter address" rows="3"></textarea>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for Delete -->
                            <div class="modal fade" id="deleteModal{{$i}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{$i}}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <!-- Your modal content for delete goes here -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Delete confirmation message or content goes here -->
                                            Are you sure you want to delete this user?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-danger">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
@endsection
