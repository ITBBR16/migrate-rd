$(document).ready(function(){
    const containerFilterSecond = $('#additional-kelengkapan-filter-second');
    const containerFilterExclude = $('#barang-exclude-filter-second');
    const tambahAdditionalFilter = $('#add-second-additional-filter');
    const tambahExcludeBarangQcSecond = $('#add-second-exclude-kelengkapan-filter')
    const jenisProdukQcSecond = $('#jenis-qc-id');
    let uniqueCount = 20;

    tambahAdditionalFilter.on('click', function () {
        uniqueCount++
        const jenisId = jenisProdukQcSecond.val();

        let addAdditonalForm = `
            <tr id="additionalKelengkapanFilter-${uniqueCount}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <label for="kelengkapan_filter_additional${uniqueCount}" class="sr-only">Jenis Paket Produk</label>
                    <select name="kelengkapan_filter_additional[]" id="kelengkapan_filter_additional${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                        <option value="" hidden>-- Tambahan Kelengkapan --</option>
                    </select>
                </th>
                <td class="px-6 py-4">
                    <label for="kondisi-${uniqueCount}" class="sr-only">Jenis Paket Produk</label>
                    <select name="additional_kondisi[]" id="kondisi-${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                        <option value="" hidden>-- Kondisi Kelengkapan --</option>
                        <option value="Sangat Baik">Sangat Baik</option>
                        <option value="Baik">Baik</option>
                        <option value="Cukup">Cukup</option>
                        <option value="Kurang">Kurang</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="additional_serial_number[]" id="serial-number-${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600" required>
                </td>
                <td class="px-6 py-4 text-right">
                    <input type="text" name="additional_keterangan[]" id="keterangan-${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600" required>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="relative z-0 w-full group flex items-center">
                        <span class="absolute start-0 font-bold text-gray-500 dark:text-gray-400">RP</span>
                        <input type="text" name="harga_satuan_filter_second[]" id="harga_satuan_filter_second-${uniqueCount}" class="harga_satuan_filter_second block py-2.5 ps-8 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-center items-center col-span-1">
                        <button type="button" class="remove-second-filter" data-id="${uniqueCount}">
                            <span class="material-symbols-outlined text-red-600 hover:text-red-500">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;

        containerFilterSecond.append(addAdditonalForm);

        if(jenisId){
            const ddQcKelengkapan = $('#kelengkapan_qc_additional' + uniqueCount);
            fetch(`/kios/product/getAdditionalKelengkapan/${jenisId}`)
            .then(response => response.json())
            .then(data => {
                ddQcKelengkapan.empty();

                const defaultOption = $('<option>', {
                    text: '-- Tambahan Kelengkapan --',
                    value: '',
                    hidden: true
                });
                ddQcKelengkapan.append(defaultOption);

                data.forEach(kelengkapan => {
                    const option = $('<option>', {
                        value: kelengkapan.id,
                        text: kelengkapan.kelengkapan
                    })
                    .addClass('dark:bg-gray-700');
                    ddQcKelengkapan.append(option);
                });
            })
            .catch(error => console.error('Error:', error));
        } else {
            ddQcKelengkapan.html('');
        }
    });

    tambahExcludeBarangQcSecond.on('click', function () {
        uniqueCount++

        let addAdditonalForm = `
        <div id="exclude-barang-filter-${uniqueCount}" class="mt-4 grid md:grid-cols-5 md:gap-6">
            <div class="relative z-0 w-full mb-6 group">
                <input type="text" name="exclude_kelengkapan_filter_additional[]" id="exclude_kelengkapan_qc_additional${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="">
                <label for="exclude_kelengkapan_qc_additional${uniqueCount}" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kelengkapan</label>
            </div>
            <div class="relative z-0 w-full mb-6 group">
                <input type="text" name="exclude_kondisi[]" id="exclude-kondisi${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="">
                <label for="exclude-kondisi${uniqueCount}" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kondisi</label>
            </div>
            <div class="relative z-0 w-full mb-6 group">
                <input type="text" name="exclude_serial_number[]" id="exclude-serial_number${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="">
                <label for="exclude-serial_number${uniqueCount}" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Serial Number</label>
            </div>
            <div class="relative z-0 w-full mb-6 group">
                <input type="text" name="exclude_keterangan[]" id="exclude-keterangan${uniqueCount}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="">
                <label for="exclude-keterangan${uniqueCount}" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Keterangan</label>
            </div>
            <div class="grid grid-cols-3">
                <div class="relative col-span-2">
                    <span class="absolute start-0 bottom-8 font-bold text-gray-500 dark:text-gray-400">RP</span>
                    <input type="text" name="harga_satuan_exclude[]" id="harga-satuan-exclude-${uniqueCount}" class="harga_satuan_filter_second block py-2.5 ps-8 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="harga-satuan-exclude-${uniqueCount}" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-placeholder-shown:start-8 peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">Biaya Satuan</label>
                </div>
                <div class="flex justify-center items-center col-span-1">
                    <button type="button" class="remove-exclude-kelengkapan-filter" data-id="${uniqueCount}">
                        <span class="material-symbols-outlined text-red-600 hover:text-red-500">delete</span>
                    </button>
                </div>
            </div>
        </div>
        `;

        containerFilterExclude.append(addAdditonalForm);
    });

    $(document).on("click", ".remove-exclude-kelengkapan-filter", function() {
        let itemNameId = $(this).data("id");
        $("#exclude-barang-filter-"+itemNameId).remove();
        uniqueCount--;
    });

    $(document).on("click", ".remove-second-filter", function() {
        let itemNameId = $(this).data("id");
        $("#additionalKelengkapanFilter-"+itemNameId).remove();
        uniqueCount--;
    });

    function formatRupiah(angka) {
        return accounting.formatMoney(angka, "", 0, ".", ",");
    }

    function updateCekBiayaFilter() {
        let totalNilai = 0;
        let buttonFilter = $("#submit_filter_second");
        let biayaAwal = $("#biaya_ambil").val();
        let parsedBiayaAwal = parseFloat(biayaAwal.replace(/\D/g, ''));

        $("#additional-kelengkapan-filter-second tr").each(function () {
            var nilaiSatuan = $(this).find("[name='harga_satuan_filter_second[]']").val();
            var parsedNilai = parseFloat(nilaiSatuan.replace(/\D/g, ''));
            
            totalNilai += parsedNilai
        });
        
        var showNilai = formatRupiah(totalNilai);
        $("#biaya_cek_filter").val(showNilai);

        if( parsedBiayaAwal === totalNilai ) {
            buttonFilter.removeClass('cursor-not-allowed');
            buttonFilter.prop("disabled", false);
        }

    }

    $(document).on('input', '.harga_satuan_filter_second', function () {
        var inputValue = $(this).val();
        inputValue = inputValue.replace(/[^\d]/g, '');
        var parsedValue = parseInt(inputValue, 10);
        $(this).val(formatRupiah(parsedValue));
    });

    $(document).on('change', '.harga_satuan_filter_second', function () {
        updateCekBiayaFilter();
    });

});
