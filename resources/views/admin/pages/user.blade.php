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
                            <th>Nama</th>
                            <th>KTP</th>
                            <th>STNK</th>
                            <th>SIM</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents->groupBy('user_id') as $groupedDocuments)
                            <tr>
                                <td>{{ $groupedDocuments->first()->user->name }}</td>
                                @foreach(['KTP', 'STNK', 'SIM'] as $type)
                                    @php
                                        $document = $groupedDocuments->firstWhere('document_type', $type);
                                    @endphp
                                    <td>
                                        @if ($document)
                                            <img style="width: 150px" src="{{ asset('storage/documents/' . $document->image) }}">
                                        @else
                                            <p>Tidak ada data {{ $type }}</p>
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    @php
                                        $verifiedAll = $groupedDocuments->every(function ($doc) {
                                            return $doc->is_verified;
                                        });
                                    @endphp

                                    @if ($verifiedAll)
                                        <p>Dokumen sudah terverifikasi semua</p>
                                    @else
                                        @foreach(['KTP', 'STNK', 'SIM'] as $type)
                                            @php
                                                $document = $groupedDocuments->firstWhere('document_type', $type);
                                            @endphp
                                            @if ($document && !$document->is_verified)
                                                <form method="POST" action="{{ route('verify.document', ['documentId' => $document->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-fw fa-check"></i> Verifikasi {{ $type }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
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
