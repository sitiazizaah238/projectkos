@extends('layouts.app')

@section('content')
    <div class="d-flex">

        @include('components.sidebar-pemilik')

        <div class="flex-grow-1">

            <div class="p-4" style="background:#f5f7fb; min-height:100vh;">

                <h3 class="fw-bold mb-4">Metode Pembayaran</h3>

                <a href="{{ route('pemilik.pembayaran.create') }}" class="btn btn-primary mb-3">
                    + Tambah Metode Baru
                </a>

                <div class="card shadow-sm rounded-4">

                    <div class="card-header bg-dark text-white">
                        Metode Pembayaran
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Metode</th>
                                    <th>Atas Nama</th>
                                    <th>No Rekening</th>
                                    <th>Gambar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($metode as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_metode }}</td>
                                        <td>{{ $item->atas_nama }}</td>
                                        <td>{{ $item->no_rekening ?? '-' }}</td>

                                        <td>
                                            @if ($item->gambar)
                                                <img src="{{ asset('storage/' . $item->gambar) }}" width="60">
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @if ($item->status == 'aktif')
                                                <span class="text-success">● Aktif</span>
                                            @else
                                                <span class="text-danger">● Non-Aktif</span>
                                            @endif
                                        </td>

                                        <td class="d-flex justify-content-center gap-2">

                                            <a href="{{ route('pemilik.pembayaran.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('pemilik.pembayaran.destroy', $item->id) }}"
      method="POST"
      class="form-delete">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-sm btn-danger btn-delete">
        <i class="bi bi-trash"></i>
    </button>
</form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum ada metode pembayaran</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {

    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {

            let form = this.closest('.form-delete');

            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data metode pembayaran akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });
    });

});
</script>
@endsection
