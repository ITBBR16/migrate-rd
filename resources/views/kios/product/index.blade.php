@extends('kios.layouts.main')
@extends($sidebarLayout)

@section('container')
    <div class="flex text-3xl font-bold mb-8 text-gray-700 border-b border-gray-400 py-3 dark:text-gray-300">
        <span>Daftar Produk Baru</span>
    </div>

    @if (session()->has('success'))
        <div id="alert-success-input" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
            <span class="material-symbols-outlined flex-shrink-0 w-4 h-4">task_alt</span>
            <div class="ml-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-success-input" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div id="alert-failed-input" class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
            <span class="material-symbols-outlined flex-shrink-0 w-5 h-5">info</span>
            <div class="ml-3 text-sm font-medium">
                {{ session('error') }}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-failed-input" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="relative overflow-x-auto mt-6">
        <div class="flex items-center justify-between py-4">
            <div class="flex flex-row gap-6">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" id="table-search" class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-52 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search. . .">
                </div>
                <div class="relative">
                    <button id="filterProdukBaruButton" data-dropdown-toggle="filterProdukBaru" data-dropdown-placement="right-start" class="text-slate-500 border-2 bg-transparent hover:bg-slate-50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-gray-700 dark:border-gray-600" type="button">FILTER  
                        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Dropdown menu -->
    <div id="filterProdukBaru" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="multiLevelDropdownButton">
            <li>
                <button id="kategoriProdukBaruButton" data-dropdown-toggle="kategoriProdukBaru" data-dropdown-placement="right-start" type="button" class="flex items-center justify-between w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Kategori Produk
                    <svg class="w-2.5 h-2.5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                </button>
                <div id="kategoriProdukBaru" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="kategoriProdukBaruButton">
                        @foreach ($kategoriProduk as $kategori)
                            <li>
                                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <input id="checkbox-kategori-baru-{{ $kategori->id }}" name="kategori_baru[]"  type="checkbox" value="{{ $kategori->id }}" class="filter-checkbox-produk-baru w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="checkbox-kategori-baru-{{ $kategori->id }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ $kategori->nama }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
            <li>
                <button id="paketProdukBaruButton" data-dropdown-toggle="paketProdukBaru" data-dropdown-placement="right-start" type="button" class="flex items-center justify-between w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Paket Penjualan<svg class="w-2.5 h-2.5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                </button>
                <div id="paketProdukBaru" class="z-10 hidden -ml-4 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="paketProdukBaruButton">
                        @foreach ($typeProduks as $type)
                            <li>
                                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <input id="checkbox-paket-baru-{{ $type->id }}" name="paket_baru[]" type="checkbox" value="{{ $type->id }}" class="filter-checkbox-produk-baru w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="checkbox-paket-baru-{{ $type->id }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ $type->type }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <div class="mt-2 flex flex-row gap-4">
        <div id="kategori-filter" class="flex items-center p-4 mb-4 text-gray-400 border border-gray-300 rounded-2xl bg-transparent max-w-fit">
            <div class="text-sm font-medium">
                Agriculture
            </div>
            <button type="button" class="ml-2 -mx-1.5 -my-1.5 rounded-lg text-gray-400 focus:ring-2 p-1.5 hover:bg-gray-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:hover:bg-gray-700"  data-dismiss-target="#kategori-filter">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        <div id="kategori-filter2" class="flex items-center p-4 mb-4 text-gray-400 border border-gray-300 rounded-2xl bg-transparent max-w-fit">
            <div class="text-sm font-medium">
                Remote Controller
            </div>
            <button type="button" class="ml-2 -mx-1.5 -my-1.5 rounded-lg text-gray-400 focus:ring-2 p-1.5 hover:bg-gray-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:hover:bg-gray-700"  data-dismiss-target="#kategori-filter2">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="relative mt-2 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-6 py-3" style="width: 30%;">
                        Jenis Produk
                    </th>
                    <th scope="col" class="px-6 py-3" style="width: 10%;">
                        Stok
                    </th>
                    <th scope="col" class="px-6 py-3" style="width: 15%;">
                        Harga Jual
                    </th>
                    <th scope="col" class="px-6 py-3" style="width: 15%;">
                        Harga Promo
                    </th>
                    <th scope="col" class="px-6 py-3" style="width: 12%;">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3" style="width: 18%;">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produks as $key => $pd)
                <tr class="bg-white border-b hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600 customer-row">
                    <td class="px-6 py-2">
                        <div class="flex flex-row items-center">
                            @php
                                $imageLink = $pd->imageprodukbaru->link_drive ?? "";
                                preg_match('/\/file\/d\/(.*?)\/view/', $imageLink, $matches);
                                $fileId = isset($matches[1]) ? $matches[1] : null;
                            @endphp
                            @if (!empty($imageLink))
                                <div class="w-14 -h-10 mr-4">
                                    <img src="https://drive.google.com/thumbnail?id={{ $fileId }}" alt="{{ $pd->subjenis->produkjenis->jenis_produk }} {{ $pd->subjenis->paket_penjualan }}">
                                </div>
                            @endif
                            <span>
                                {{ $pd->subjenis->produkjenis->jenis_produk }} {{ $pd->subjenis->paket_penjualan }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-2">
                        {{ $pd->serialnumber->where('produk_id', $pd->id)->where('status', 'Ready')->count() }}
                    </td>
                    <td class="px-6 py-2">
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-base font-bold text-gray-700 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-300 dark:border-gray-600">RP</span>
                            <input type="text" id="update-srp-baru-{{ $pd->id }}" data-id="{{ $pd->id }}" class="update-srp-baru rounded-none rounded-e-lg bg-gray-50 border text-base text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-12 border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="0" value="{{ number_format($pd->srp, 0, ',', '.') }}">
                        </div>
                    </td>
                    <td class="px-6 py-2">
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-base font-bold text-gray-700 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-300 dark:border-gray-600">RP</span>
                            <input type="text" class="update-srp-baru rounded-none rounded-e-lg bg-gray-50 border text-base text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-12 border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="0" value="{{ number_format($pd->harga_promo, 0, ',', '.') }}" disabled>
                        </div>
                    </td>
                    <td class="px-6 py-2">
                        <span class="bg-{{ ($pd->status == 'Ready') ? 'green' : (($pd->status == 'Promo') ? 'red' : 'gray') }}-500 text-white font-medium me-2 px-2.5 py-0.5 rounded-full">{{ $pd->status }}</span>
                    </td>
                    <td class="px-6 py-2">
                        <div class="flex flex-wrap">
                            <button type="button" data-modal-target="view-detail-produk{{ $pd->id }}" data-modal-toggle="view-detail-produk{{ $pd->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                <i class="material-symbols-outlined text-base">visibility</i>
                            </button>
                            @if (auth()->user()->is_admin === 1 || auth()->user()->is_admin === 2)
                                <button type="button" data-modal-target="update-produk{{ $pd->id }}" data-modal-toggle="update-produk{{ $pd->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                    <i class="material-symbols-outlined text-base">edit</i>
                                </button>
                                <button type="button" data-modal-target="delete-produk{{ $pd->id }}" data-modal-toggle="delete-produk{{ $pd->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                    <i class="material-symbols-outlined text-base">delete</i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 ">
            {{-- {{ $dataCustomer->links() }} --}}
        </div>
    </div>

    {{-- Modal Daftar Produk --}}
    @include('kios.product.modal.modal-view-daftar-produk')
    @include('kios.product.modal.modal-edit-daftar-produk')
    @include('kios.product.modal.modal-delete-daftar-produk')
    
@endsection
