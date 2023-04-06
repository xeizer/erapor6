<div>
    @include('panels.breadcrumb')
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success" role="alert">
                        <div class="alert-body">{{ session('message') }}</div>
                    </div>
                @endif
                @include('components.navigasi-table')
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Mata Pelajaran</th>
                            <th class="text-center">Fase</th>
                            <th class="text-center">Elemen</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="text-center">Jumlah Tujuan Pembelajaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($collection->count())
                            @foreach($collection as $item)
                            <?php
                            if($item->aktif){
                                $icon_aktif 	= 'fa-close';
                                $title_aktif	= 'Non Aktifkan';
                            } else {
                                $icon_aktif 	= 'fa-check';
                                $title_aktif	= 'Aktifkan';
                            }
                            ?>
                            <tr>
                                <td class="align-top">{{$item->mata_pelajaran->nama}}</td>
                                <td class="text-center align-top">
                                    {{$item->fase}}
                                </td>
                                <td class="align-top">{{$item->elemen}}</td>
                                <td class="align-top">{{$item->deskripsi}}</td>
                                <td class="text-center align-top">{{$item->tp_count}}</td>
                                <td class="text-center align-top">
                                    {!! ($item->aktif) ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Non Aktif</span>' !!}
                                </td>
                                <td class="text-center align-top">
                                    @if($item->aktif)
                                    <button class="btn btn-sm btn-danger" wire:click="getId({{$item->cp_id}}, 0)">Non Aktifkan</button>
                                    @else
                                    <button class="btn btn-sm btn-success" wire:click="getId({{$item->cp_id}}, 1)">Aktifkan</button>
                                    @endif
                                    <button class="btn btn-warning" wire:click="editcapaiankompetensi({{$item->cp_id}})">Edit</button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td class="text-center" colspan="8">Tidak ada data untuk ditampilkan</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="row justify-content-between mt-2">
                    <div class="col-6">
                        @if($collection->count())
                        <p>Menampilkan {{ $collection->firstItem() }} sampai {{ $collection->firstItem() + $collection->count() - 1 }} dari {{ $collection->total() }} data</p>
                        @endif
                    </div>
                    <div class="col-6">
                        {{ $collection->onEachSide(1)->links('components.custom-pagination-links-view') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal edit --}}
    <div wire:ignore.self class="modal fade" id="editCP" tabindex="-1" aria-labelledby="pembelajaranModalLabel"
        aria-hidden="true" data-bs-backdrop="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pembelajaranModalLabel">Pembelajaran Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-2">
                        <label for="elemen" class="col-sm-3 col-form-label">Elemen</label>
                        <div class="col-sm-9">
                            <input type="text" wire:model="elemen" id="elemen" class="form-control" aria-describedby="elemen"/>
                            @error('elemen')
                            <span id="elemen" class="mt-1">
                                <span class="text-danger">{{ $message }}</span>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="capaian_pembelajaran" class="col-sm-3 col-form-label">Capaian Pembelajaran</label>
                        <div class="col-sm-9">
                            <textarea rows="5" wire:model="capaian_pembelajaran" id="capaian_pembelajaran" class="form-control" aria-describedby="capaian_pembelajaran"></textarea>
                            @error('capaian_pembelajaran')
                            <span id="capaian_pembelajaran" class="mt-1">
                                <span class="text-danger">{{ $message }}</span>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:target="simpan_cp" wire:loading.remove>Tutup</button>
                    <div class="spinner-border text-primary" role="status" wire:loading wire:target="simpan_cp">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <button type="button" class="btn btn-primary" wire:click.prevent="simpan_cp" wire:target="simpan_cp" wire:loading.remove>Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @include('components.loader')
</div>
@push('scripts')
<script>
    Livewire.on('editCP', event => {

        $('#editCP').modal('show');
    })
    Livewire.on('close-modal', event => {
        $('#editCP').modal('hide');
    })
</script>
@endpush
