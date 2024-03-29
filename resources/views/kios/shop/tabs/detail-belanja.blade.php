<div class="hidden p-4" id="dbl" role="tabpanel" aria-labelledby="dbl-tab">
    <div class="relative overflow-x-auto">
        <div class="flex items-center justify-between py-4">
            <div class="flex text-xl">
                <span class="text-gray-700 font-semibold dark:text-gray-300">List Belanja</span>
            </div>
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" id="table-search" class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-52 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Items">
            </div>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Order ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Supplier
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Pembelian
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Nominal
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr class="bg-white border-b hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600">
                        <th class="px-6 py-2">
                            N.{{ $item->id }}
                        </th>
                        <td class="px-6 py-2">
                            {{ $item->supplier->nama_perusahaan }}
                        </td>
                        <td class="px-6 py-2">
                            {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM YYYY') }}
                        </td>
                        <td class="px-6 py-2">
                            @php
                                $totalNilai = 0;
                            @endphp
                            @foreach ($item->orderLists as $order)
                                @php
                                    $totalNilai += $order->quantity * $order->nilai;
                                @endphp
                            @endforeach
                            Rp. {{ number_format($totalNilai, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-2">
                            @if ($item->status == 'Belum Validasi')
                                <span class="bg-orange-400 rounded-md px-2 py-0 text-white">{{ $item->status }}</span>
                            @else
                                <span class="bg-green-400 rounded-md px-2 py-0 text-white">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-2">
                            <div class="flex flex-wrap">
                                <button type="button" data-modal-target="view-order-new{{ $item->id }}" data-modal-toggle="view-order-new{{ $item->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                    <i class="material-symbols-outlined text-base">visibility</i>
                                </button>
                                @if ($item->status == 'Belum Validasi')
                                    <button type="button" data-modal-target="validasi-order{{ $item->id }}" data-modal-toggle="validasi-order{{ $item->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                        <i class="material-symbols-outlined text-base">task_alt</i>
                                    </button>
                                    <button type="button" data-modal-target="delete-belanja{{ $item->id }}" data-modal-toggle="delete-belanja{{ $item->id }}" class="text-gray-400 hover:text-gray-800 mx-2 dark:hover:text-gray-300">
                                        <i class="material-symbols-outlined text-base">delete</i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Modal Action --}}
    @include('kios.shop.modal.view-new')
    @include('kios.shop.modal.validasi-new')
    @include('kios.shop.modal.delete-new')
</div>