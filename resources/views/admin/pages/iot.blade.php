@extends('admin/components/layout')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Table Sensor</h1>


        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="m-0 font-weight-bold text-primary">DataTables Sensor</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i
                                class="fas fa-plus"></i> Tambah</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Longitude</th>
                                <th>Latitude</th>
                                <th>Radius</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sensors as $sensor)
                                <tr>
                                    <td>{{ $sensor->code }}</td>
                                    <td>{{ $sensor->longitude }}</td>
                                    <td>{{ $sensor->latitude }}</td>
                                    <td>{{ $sensor->radius }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                            data-target="#editModal{{ $sensor->id }}"><i
                                                class="fas fa-fw fa-edit"></i>Edit</button>
                                        <button class="btn btn-danger" data-toggle="modal"
                                            data-target="#deleteModal{{ $sensor->id }}"><i
                                                class="fas fa-fw fa-trash"></i>Hapus</button>

                                    </td>
                                </tr>
                                <!-- Modal for Edit -->
                                <div class="modal fade" id="editModal{{ $sensor->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="editModalLabel{{ $sensor->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <!-- Your modal content for edit goes here -->
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $sensor->id }}">Edit Sensor
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="{{ route('sensor.update', $sensor->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="code">Name</label>
                                                        <input type="text" class="form-control" id="code"
                                                            name="code" value="{{ $sensor->code }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="longitude">Longitude</label>
                                                        <input type="text" class="form-control" id="longitude"
                                                            name="longitude" value="{{ $sensor->longitude }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="latitude">Latitude</label>
                                                        <input type="text" class="form-control" id="latitude"
                                                            name="latitude" value="{{ $sensor->latitude }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="latitude">Radius</label>
                                                        <input type="number" class="form-control" id="latitude"
                                                            name="latitude" value="{{ $sensor->radius }}">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal for Add -->
                                <div class="modal fade" id="addModal" tabindex="-1" role="dialog"
                                    aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addModalLabel">Tambah Sensor</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="{{ route('sensor.store') }}">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="code">Name</label>
                                                        <input type="text" class="form-control" id="code"
                                                            name="code">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="longitude">Longitude</label>
                                                        <input type="text" class="form-control" id="longitude"
                                                            name="longitude">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="latitude">Latitude</label>
                                                        <input type="text" class="form-control" id="latitude"
                                                            name="latitude">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="latitude">Radius</label>
                                                        <input type="number" class="form-control" id="radius"
                                                            name="radius">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Tambah</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Modal for Delete -->
                                <div class="modal fade" id="deleteModal{{ $sensor->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="deleteModalLabel{{ $sensor->id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $sensor->id }}">Delete
                                                    Sensor</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this Sensor?
                                            </div>
                                            <div class="modal-footer">
                                                <form method="post" action="{{ route('sensor.destroy', $sensor->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
