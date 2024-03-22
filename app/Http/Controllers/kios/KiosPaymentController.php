<?php

namespace App\Http\Controllers\kios;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\kios\KiosPayment;
use App\Http\Controllers\Controller;
use App\Models\ekspedisi\PengirimanEkspedisi;
use App\Models\kios\KiosMetodePembayaran;
use App\Models\kios\KiosMetodePembayaranSecond;
use App\Models\kios\KiosOrder;
use App\Models\kios\KiosOrderSecond;
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
        $payment = KiosPayment::with('order.supplier', 'metodepembayaran', 'ordersecond', 'metodepembayaransecond')->get();

        return view('kios.shop.payment.payment', [
            'title' => 'Payment',
            'active' => 'payment',
            'navActive' => 'product',
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
            $orderId = $request->input('order_id');
            $statusOrder = $request->input('status_order');
            $noTransaksi = ($statusOrder == 'Baru') ? 'KiosBaru-' : 'KiosBekas-';
            $keteranganFinance = ($statusOrder == 'Baru') ? 'Order Id N.' . $id : 'Order Id S.' . $id;

            $totalBelanja = preg_replace("/[^0-9]/", "", $request->input('nilai_belanja'));
            $totalOngkir = preg_replace("/[^0-9]/", "", $request->input('ongkir'));
            $totalPajak = preg_replace("/[^0-9]/", "", $request->input('pajak'));
    
            $urlFinance = 'https://script.google.com/macros/s/AKfycbzBE9VL6syqbKmLYxur9vffg9uJiNdV-Nu8Vg-RL1aEE7U_0WP6vqzg09FOrlZJD1uTfg/exec';
            $dataFinance = [
                'tanggal' => $formattedDate,
                'divisi' => $divisiName,
                'no_transaksi' => $noTransaksi . $id,
                'supplier_kios' => $request->input('supplier_kios'),
                'invoice' => '0',
                'media_transaksi' => $request->input('media_transaksi'),
                'no_rek' => $request->input('no_rek'),
                'nama_akun' => $request->input('nama_akun'),
                'nilai_belanja' => $totalBelanja,
                'ongkir' => $totalOngkir,
                'pajak' => $totalPajak,
                'keterangan' => $keteranganFinance . ", " . $request->input('keterangan'),
            ];
    
            $response = Http::post($urlFinance, $dataFinance);
            
            if($response->successful()) {
                
                $kiosPayment->keterangan = $request->input('keterangan');
                $kiosPayment->tanggal_request = $formattedDate;
                $kiosPayment->status = 'Waiting For Payment';
    
                // Message to finance group
                if($statusOrder == 'Baru') {
                    $orderBaru = KiosOrder::findOrFail($orderId);
                    // $orderBaru->status = 'Waiting For Payment';
                    // $orderBaru->save();
                    $linkDrive = $orderBaru->link_drive;

                    // $kiosPayment->save();
                    // PengirimanEkspedisi::create([
                    //     'divisi_id' => $divisiId,
                    //     'order_id' => $orderId,
                    //     'status_order' => 'Baru',
                    //     'status' => 'Unprocess',
                    // ]);
                } else {
                    // $orderSecond = KiosOrderSecond::findOrFail($orderId);
                    // $orderSecond->status = 'Waiting For Payment';
                    // $orderSecond->save();
                }

                $totalPembayaran = $totalBelanja + $totalOngkir + $totalPajak;
                $formattedTotal = number_format($totalPembayaran, 0, ',', '.');
                $msgOrderId = ($statusOrder == 'Baru') ? 'N.' . $orderId : 'S.' . $orderId;
                $namaGroup = '';
                $header = "*Incoming Request Payment Produk Baru*\n\n";
                $body = "Order ID : " . $msgOrderId . "\nRef : " . $noTransaksi . $id . "\nTotal Nominal : Rp. " . $formattedTotal . "\nLink Order ID : ";
                $footer = "Ditunggu Paymentnya kakak 😘\nJangan lupa Upload Bukti Transfer di Link Drive yaa\n";
                $message = $header . $body . $footer;
    
                $urlMessage = 'https://script.google.com/macros/s/AKfycbxX0SumzrsaMm-1tHW_LKVqPZdIUG8sdp07QBgqmDsDQDIRh2RHZj5gKZMhAb-R1NgB6A/exec';
                $messageFinance = [
                    'no_telp' => '6285156519066',
                    'pesan' => $message,
                ];
    
                $responseMessage = Http::post($urlMessage, $messageFinance);

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
            $statusOrder = $request->input('status_order');
            
            if($request->has('new-metode-payment-edit')){
                if($statusOrder === 'Baru') {
                    $validate = $request->validate([
                                    'supplier_id' => 'required',
                                    'media_pembayaran' => 'required',
                                    'no_rek' => ['required', Rule::unique('rumahdrone_kios.metode_pembayaran_supplier', 'no_rek')],
                                    'nama_akun' => 'required',
                                ]);
        
                    $metodePembayaran = KiosMetodePembayaran::create($validate);
                    $kiosPayment->metode_pembayaran_id = $metodePembayaran->id;
                    $kiosPayment->save();
                } else {
                    $validate = $request->validate([
                        'customer_id' => 'required',
                        'media_pembayaran' => 'required',
                        'no_rek' => ['required', Rule::unique('rumahdrone_kios.kios_metode_pembayaran_second', 'no_rek')],
                        'nama_akun' => 'required',
                    ]);

                    $metodePembayaran = KiosMetodePembayaranSecond::create($validate);
                    $kiosPayment->metode_pembayaran_id = $metodePembayaran->id;
                    $kiosPayment->save();
                }
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
