<?php

namespace App\Http\Controllers\kios;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\kios\KiosPayment;
use App\Http\Controllers\Controller;
use App\Models\ekspedisi\PengirimanEkspedisi;
use App\Models\kios\KiosMetodePembayaran;
use App\Repositories\kios\KiosRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class KiosPaymentController extends Controller
{
    public function __construct(private KiosRepository $suppKiosRepo){}

    public function index()
    {
        $user = auth()->user();
        $divisiName = $this->suppKiosRepo->getDivisi($user);
        $payment = KiosPayment::with('order.supplier', 'metodepembayaran')->get();
        $metodePembayaran = KiosMetodePembayaran::all();

        return view('kios.shop.payment.payment', [
            'title' => 'Payment',
            'active' => 'payment',
            'dropdown' => '',
            'dropdownShop' => '',
            'divisi' => $divisiName,
            'payment' => $payment,
        ]);
    }

    public function validasi(Request $request, $id)
    {

        $request->validate([
            'media_transaksi' => 'required',
            'no_rek' => 'required',
            'nama_akun' => 'required',
        ]);

        try {

            $user = auth()->user();
            $divisiId = $user->divisi_id;
            $divisi = $this->suppKiosRepo->getDivisi($user);
            $divisiName = $divisi->nama;
            $tanggal = Carbon::now();
            $tanggal->setTimezone('Asia/Jakarta');
            $formattedDate = $tanggal->format('d/m/Y H:i:s');
            $kiosPayment = KiosPayment::findOrFail($id);
    
            $totalBelanja = preg_replace("/[^0-9]/", "", $request->input('nilai_belanja'));
            $totalOngkir = preg_replace("/[^0-9]/", "", $request->input('ongkir'));
            $totalPajak = preg_replace("/[^0-9]/", "", $request->input('pajak'));
    
            $urlFinance = 'https://script.google.com/macros/s/AKfycbzBE9VL6syqbKmLYxur9vffg9uJiNdV-Nu8Vg-RL1aEE7U_0WP6vqzg09FOrlZJD1uTfg/exec';
            $dataFinance = [
                'tanggal' => $formattedDate,
                'divisi' => $divisiName,
                'no_transaksi' => 'Kios-' . $id,
                'supplier_kios' => $request->input('supplier_kios'),
                'invoice' => $request->input('invoice'),
                'media_transaksi' => $request->input('media_transaksi'),
                'no_rek' => $request->input('no_rek'),
                'nama_akun' => $request->input('nama_akun'),
                'nilai_belanja' => $totalBelanja,
                'ongkir' => $totalOngkir,
                'pajak' => $totalPajak,
                'keterangan' => $request->input('keterangan'),
            ];
    
            $response = Http::post($urlFinance, $dataFinance);
            
            $kiosPayment->keterangan = $request->input('keterangan');
            $kiosPayment->tanggal_request = $formattedDate;
            $kiosPayment->status = 'Waiting For Payment';
            
            if($response->successful()) {
                $kiosPayment->save();
                PengirimanEkspedisi::create([
                    'divisi_id' => $divisiId,
                    'order_id' => $request->input('order_id'),
                    'status_order' => 'Baru',
                    'status' => 'Unprocess',
                ]);
    
                return back()->with('success', 'Success Request Payment.');
    
            } else {
                return back()->with('error', 'Cant Send To Request Payment.');
            }

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }


    }

    public function update(Request $request, $id)
    {
        try {
            $kiosPayment = KiosPayment::findOrFail($id);
    
            $ongkir = preg_replace("/[^0-9]/", "", $request->input('ongkir'));
            $pajak = preg_replace("/[^0-9]/", "", $request->input('pajak'));
            
            if($request->has('new-metode-payment-edit')){
                $validate = $request->validate([
                                'supplier_id' => 'required',
                                'media_pembayaran' => 'required',
                                'no_rek' => ['required', Rule::unique('rumahdrone_kios.metode_pembayaran', 'no_rek')],
                                'nama_akun' => 'required',
                            ]);
    
                $metodePembayaran = KiosMetodePembayaran::create($validate);
                $kiosPayment->metode_pembayaran_id = $metodePembayaran->id;
                $kiosPayment->save();
            }
    
            $jenisPembayaran = [];
            $jenisPembayaran[] = 'Pembelian Barang';
    
            if ($ongkir > 0) {
                $jenisPembayaran[] = 'Ongkir';
            }
    
            if ($pajak > 0) {
                $jenisPembayaran[] = 'Pajak';
            }
    
            $jenisPembayaranStr = implode(', ', $jenisPembayaran);
    
            $kiosPayment->update([
                'jenis_pembayaran' => $jenisPembayaranStr,
                'ongkir' => $ongkir,
                'pajak' => $pajak,
            ]);
    
            return back()->with('success', 'Success Update Data Payment.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }

    public function updatePayment($id)
    {
        $paymentKios = KiosPayment::findOrFail($id);
        $paymentKios->status = 'Paid';

        return response()->json(['message' => 'Product status updated successfully']);
    }

}
