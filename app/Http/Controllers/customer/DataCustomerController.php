<?php

namespace App\Http\Controllers\customer;

use Illuminate\Http\Request;
use App\Models\divisi\Divisi;
use Illuminate\Validation\Rule;
use App\Models\wilayah\Provinsi;
use App\Models\customer\Customer;
use App\Http\Controllers\Controller;
use App\Repositories\customer\CustomerRepository;
use Illuminate\Support\Facades\Http;

class DataCustomerController extends Controller
{
    public function __construct(private CustomerRepository $customerRepo){}

    public function index() 
    {
        $dataCustomer = $this->customerRepo->getAll();
        $dataKota = $this->customerRepo->getSelectKota();
        $dataKecamatan = $this->customerRepo->getSelectKecamatan();
        $dataKelurahan = $this->customerRepo->getSelectKelurahan();
        $provinsi = Provinsi::all();

        $divisiId = auth()->user()->divisi_id;
        $divisiName = Divisi::find($divisiId);

        return view('customer.main.data-customer', [
            'title' => 'Detail Customer',
            'active' => 'detail-customer',
            'dataCustomer' => $dataCustomer,
            'dataKota' => $dataKota,
            'dataKecamatan' => $dataKecamatan,
            'dataKelurahan' => $dataKelurahan,
            'provinsi' => $provinsi,
            'divisi' => $divisiName,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'nullable|email:dns',
            'instansi' => 'max:50',
            'provinsi' => 'required',
            'kota_kabupaten' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required|numeric|digits:5',
            'nama_jalan' => 'required|max:255'
        ];
        dd($request);
        
        if($request->no_telpon != $customer->no_telpon) {
            $rules['no_telpon'] = ['required', 'regex:/^62\d{9,}$/', Rule::unique('rumahdrone_customer.customer', 'no_telpon')];
        }

        $validate = $request->validate($rules);

        $updateUrl = 'https://script.google.com/macros/s/AKfycbyP1p8iZJsmM5ufLroaG7y3M1jlk7uJFwuwN6koluDVZ6fhsb4ehn8J1c1Pri6NVGmn/exec';
        $newPhoneNumber = ($request->no_telpon != $customer->no_telpon) ? $validate['no_telpon'] : '';

        $response = Http::post($updateUrl, [
            'phoneNumberToSearch' => $customer->no_telpon,
            'newFirstName' => $validate['first_name'],
            'newLastName' => $validate['last_name'],
            'newPhoneNumber' => $newPhoneNumber,
        ]);

        if($response->successful()) {
            $customer->update($validate);
            
            return redirect('/customer/data-customer')->with('success', 'Success Update Customer Data.');
        } else {
            return back()->with('error', 'Failed to Update Customer Data.');
        }

    }

    public function destroy($id)
    {
        Customer::destroy($id);

        return back()->with('success', 'Success Delete Data');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $dataCustomer = Customer::where(function($q) use ($query) {
                $q->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"]);
            })
            ->orWhere('no_telpon', 'like', "%$query%")
            ->paginate(10);

        return response()->json($dataCustomer);
    }

}
