<?php

namespace App\Http\Livewire\Referensi;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Capaian_pembelajaran;
use App\Models\Pembelajaran;
//pakmaja
use App\Models\Rombongan_belajar;

class CapaianPembelajaran extends Component
{
    use WithPagination, LivewireAlert;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $semester_id;
    public $dataedit;
    public $elemen;
    public $capaian_pembelajaran;
    public $data_rombongan_belajar = [];
    public $data_pembelajaran = [];
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function loadPerPage(){
        $this->resetPage();
    }
    public $sortby = 'mata_pelajaran_id';
    public $sortbydesc = 'ASC';
    public $per_page = 10;

    public $cp_id;
    public $data;

    public function render()
    {
        return view('livewire.referensi.capaian-pembelajaran', [
            'collection' => Capaian_pembelajaran::with(['mata_pelajaran'])->withCount('tp')->where(function($query){
                $query->whereHas('pembelajaran', function($query){
                    $query->where('guru_id', session('guru_id'));
                    $query->whereNotNull('kelompok_id');
                    $query->whereNotNull('no_urut');
                    $query->whereNull('induk_pembelajaran_id');
                    $query->where('sekolah_id', session('sekolah_id'));
                    $query->where('semester_id', session('semester_aktif'));
                    $query->orWhere('guru_pengajar_id', session('guru_id'));
                    $query->whereNotNull('kelompok_id');
                    $query->whereNotNull('no_urut');
                    $query->whereNull('induk_pembelajaran_id');
                    $query->where('sekolah_id', session('sekolah_id'));
                    $query->where('semester_id', session('semester_aktif'));
                });
            })->orderBy($this->sortby, $this->sortbydesc)
            ->orderBy('updated_at', $this->sortbydesc)
                ->when($this->search, function($query) {
                    $query->where('elemen', 'ILIKE', '%' . $this->search . '%');
                    $query->whereHas('mata_pelajaran', function($query){
                        $query->where('nama', 'ILIKE', '%' . $this->search . '%');
                    });
                    $query->whereHas('pembelajaran', function($query){
                        $query->where('nama_mata_pelajaran', 'ILIKE', '%' . $this->search . '%');
                        $query->where('guru_id', session('guru_id'));
                        $query->whereNotNull('kelompok_id');
                        $query->whereNotNull('no_urut');
                        $query->whereNull('induk_pembelajaran_id');
                        $query->where('sekolah_id', session('sekolah_id'));
                        $query->where('semester_id', session('semester_aktif'));
                        $query->orWhere('nama_mata_pelajaran', 'ILIKE', '%' . $this->search . '%');
                        $query->where('guru_pengajar_id', session('guru_id'));
                        $query->whereNotNull('kelompok_id');
                        $query->whereNotNull('no_urut');
                        $query->whereNull('induk_pembelajaran_id');
                        $query->where('sekolah_id', session('sekolah_id'));
                        $query->where('semester_id', session('semester_aktif'));
                    });
            })->paginate($this->per_page),
            'breadcrumbs' => [
                ['link' => "/", 'name' => "Beranda"], ['link' => '#', 'name' => 'Referensi'], ['name' => "Capaian Pembelajaran"]
            ],
            'tombol_add' => [
                'wire' => '',
                'link' => '/referensi/capaian-pembelajaran/tambah',
                'color' => 'primary',
                'text' => 'Tambah Data'
            ]
        ]);
    }
    private function loggedUser(){
        return auth()->user();
    }
    public function getId($cp_id, $aksi){
        $this->cp_id = $cp_id;
        $this->data = Capaian_pembelajaran::find($this->cp_id);
        $data = $this->data;
        $data->aktif = ($aksi) ? 1 : 0;
        $data->save();
        if($aksi){
            $this->alert('success', 'Data CP berhasil di aktifkan!', [
                'toast' => false
            ]);
        } else {
            $this->alert('success', 'Data CP berhasil di nonaktifkan!', [
                'toast' => false
            ]);
        }
    }
    public function editcapaiankompetensi($idnya)
    {
        $this->dataedit = Capaian_pembelajaran::where('cp_id', $idnya)->first();

        $this->elemen = $this->dataedit->elemen;
        $this->capaian_pembelajaran = $this->dataedit->deskripsi;

        $this->emit('editCP');

    }
    //custom
    public function changeTingkat(){
        $this->data_rombongan_belajar = Rombongan_belajar::select('rombongan_belajar_id', 'nama')->where(function($query){
            $query->where('tingkat', $this->tingkat);
            $query->where('semester_id', session('semester_aktif'));
            $query->where('sekolah_id', session('sekolah_id'));
            $query->whereHas('pembelajaran', $this->kondisi());
        })->get();
    }
    private function kondisi(){
        return function($query){
            if($this->rombongan_belajar_id){
                $query->where('rombongan_belajar_id', $this->rombongan_belajar_id);
            }
            $query->where('guru_id', $this->loggedUser()->guru_id);
            $query->whereNotNull('kelompok_id');
            $query->whereNotNull('no_urut');
            $query->whereHas('rombongan_belajar', function($query){
                $query->whereHas('kurikulum', function($query){
                    $query->where('nama_kurikulum', 'ILIKE', '%Merdeka%');
                });
            });
            $query->orWhere('guru_pengajar_id', $this->loggedUser()->guru_id);
            if($this->rombongan_belajar_id){
                $query->where('rombongan_belajar_id', $this->rombongan_belajar_id);
            }
            $query->whereNotNull('kelompok_id');
            $query->whereNotNull('no_urut');
            $query->whereHas('rombongan_belajar', function($query){
                $query->whereHas('kurikulum', function($query){
                    $query->where('nama_kurikulum', 'ILIKE', '%Merdeka%');
                });
            });
        };
    }
    public function changeRombel(){
        $this->data_pembelajaran = Pembelajaran::where($this->kondisi())->orderBy('mata_pelajaran_id', 'asc')->get();
    }
    // public function store(){
    //     $this->validate();
    //     if($this->tingkat == 10){
    //         $fase = 'E';
    //     } else {
    //         $fase = 'F';
    //     }
    //     $last_id_ref = Capaian_pembelajaran::where('is_dir', 1)->count();
    //     $last_id_non_ref = Capaian_pembelajaran::where('is_dir', 0)->count();
    //     $cp_id = $last_id_ref + 1000;
    //     if($last_id_non_ref){
    //         $cp_id = ($last_id_ref + $last_id_non_ref) + 1;
    //     }
    //     $this->simpan_cp($cp_id, $fase);
    //     session()->flash('message', 'Data Capaian Pembelajaran Berhasil disimpan');
    //     return redirect()->to('/referensi/capaian-pembelajaran');
    // }
    public function simpan_cp(){

        $this->dataedit->elemen = $this->elemen;
        $this->dataedit->deskripsi = $this->capaian_pembelajaran;
        $this->dataedit->save();
        $this->dataedit=null;
        $this->emit('close-modal');

    }
    //akhir
    public function perbaharui(){
        $this->emit('close-modal');
    }
}
