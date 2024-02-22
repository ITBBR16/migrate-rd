<?php

namespace App\Http\Controllers\kios;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\produk\ProdukJenis;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\kios\KiosImageSecond;
use Illuminate\Support\Facades\Http;
use App\Models\kios\KiosProdukSecond;
use App\Models\kios\KiosQcProdukSecond;
use App\Models\produk\ProdukKelengkapan;
use App\Models\produk\ProdukSubJenis;
use App\Repositories\kios\KiosRepository;

class KiosBuatPaketSecondController extends Controller
{
    public function __construct(private KiosRepository $suppKiosRepo){}

    public function index()
    {
        $user = auth()->user();
        $divisiName = $this->suppKiosRepo->getDivisi($user);
        $dataKelengkapan = KiosQcProdukSecond::with('kelengkapans')->get();
        $kiosProduks = ProdukJenis::with('subjenis.kelengkapans')->get();
        
        return view('kios.product.add-produk-second', [
            'title' => 'Create Paket Second',
            'active' => 'create-paket-second',
            'dropdown' => '',
            'dropdownShop' => '',
            'divisi' => $divisiName,
            'kelengkapansecond' => $dataKelengkapan,
            'kiosproduks' => $kiosProduks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket_penjualan_produk_second' => 'required',
            'cc_produk_second' => 'required',
            'modal_produk_second' => 'required|min:1',
            'harga_jual_produk_second' => 'required',
            'kelengkapan_second' => 'required|array',
        ]);

        try {
            $srpSecond = preg_replace("/[^0-9]/", "", $request->input('harga_jual_produk_second'));
            $snSecond = $request->input('sn_second');

            if(count(array_unique($snSecond)) !== count($snSecond)) {
                return back()->with('error', 'Serial Number tidak boleh ada yang sama.');
            }

            $randString = Str::random(10);
            $snProduk = 'Sec-' . $randString;

            $urlApiProdukSecond = 'https://script.google.com/macros/s/AKfycbwzPkDQn1MbdVOHLRfozYviDzoIl3UwfvTeCLyIuLo--_azk7oqNitRFBt6XAlhpKB3bg/exec';
            $idProdukSecond = $request->paket_penjualan_produk_second;
            $findNama = ProdukSubJenis::findOrFail($idProdukSecond);
            $namaProdukSecond = $findNama->produkjenis->jenis_produk . " " . $findNama->paket_penjualan;

            $fileData = base64_encode($request->file('file_paket_produk')->get());
            $filesDataKelengkapan = $request->file('file_kelengkapan_produk');
            $originFileName = $request->file('file_paket_produk')->getClientOriginalName();

            $payload = [
                'status' => 'Second',
                'nama_produk' => $namaProdukSecond,
                'file_upload' => $fileData,
                'file_paket_produk' => $originFileName,
                'additional_files' => [],
            ];

            foreach ($filesDataKelengkapan as $fileKelengkapan) {
                $fileData = base64_encode($fileKelengkapan->get());
                $fileName = $fileKelengkapan->getClientOriginalName();
                
                $payload['additional_files'][] = [
                    'file_upload' => $fileData,
                    'file_name' => $fileName,
                ];
            }

            $response = Http::post($urlApiProdukSecond, $payload);
            $linkFileSecond = json_decode($response->body(), true);

            $statusFile = $linkFileSecond['status'];
            $paketFile = $linkFileSecond['file_url'];
            $kelengkapanFile = $linkFileSecond['additional_file_urls'];

            if( $statusFile == 'success' ) {

                $imageProductSecond = new KiosImageSecond();
                $imageProductSecond->sub_jenis_id = $idProdukSecond;
                $imageProductSecond->use_for = 'Paket';
                $imageProductSecond->nama = $namaProdukSecond;
                $imageProductSecond->link_drive = $paketFile;
                $imageProductSecond->save();

                foreach($kelengkapanFile as $kelengkapan) {
                    $kelengkapanImage = new KiosImageSecond();
                    $kelengkapanImage->sub_jenis_id = $idProdukSecond;
                    $kelengkapanImage->use_for = 'Kelengkapan';
                    $kelengkapanImage->nama = $kelengkapan['nama'];
                    $kelengkapanImage->link_drive = $kelengkapan['url'];
                    $kelengkapanImage->save();
                }

                $produkSecond = KiosProdukSecond::create([
                    'sub_jenis_id' => $request->input('paket_penjualan_produk_second'),
                    'srp' => $srpSecond,
                    'cc_produk_second' => $request->input('cc_produk_second'),
                    'serial_number' => $snProduk,
                    'status' => 'Ready',
                ]);

                foreach($snSecond as $item) {
                    DB::connection('rumahdrone_produk')
                            ->table('kios_kelengkapan_second_list')
                            ->where('pivot_qc_id', $item)
                            ->update(['kios_produk_second_id' => $produkSecond->id, 'status' => 'On Sell']);
                }
    
                return back()->with('success', 'Success Created Product Second.');
            }


        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getKelengkapanSecond()
    {
        $data = KiosQcProdukSecond::with(['kelengkapans' => function ($query) {
            $query->wherePivot('status', 'Ready');
        }])->get();
    
        return response()->json($data);
    }

    public function getSNSecond($id)
    {
        $data = KiosQcProdukSecond::with(['kelengkapans' => function ($query) use($id) {
            $query->wherePivot('produk_kelengkapan_id', $id)
                  ->wherePivot('status', 'Ready');
        }])->get();
    
        return response()->json($data);
    }

    public function getPriceSecond($id)
    {
        $dataPrice = DB::connection('rumahdrone_produk')
                        ->table('kios_kelengkapan_second_list')
                        ->where('pivot_qc_id', $id)
                        ->pluck('harga_satuan');

        return response()->json($dataPrice);
    }

}
