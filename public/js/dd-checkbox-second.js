document.addEventListener('DOMContentLoaded', function(){
    const jenisDroneSecond = document.getElementById('jenis_drone_second');
    const container = document.getElementById('kelengkapan-second');
    
    jenisDroneSecond.addEventListener('change', function() {
        const subJenisId = jenisDroneSecond.value;
        
        if(subJenisId){
            fetch(`/kios/get-kelengkapan-second/${subJenisId}`)
            .then(response => response.json())
            .then(data => {

                container.innerHTML = '';

                data.kelengkapans.forEach(function (kelengkapan) {
                    container.innerHTML += `
                    <div class="grid md:grid-cols-3 md:gap-6">
                        <div class="flex items-center mb-6">
                            <input name="kelengkapan_second[]" id="kelengkapan_second${kelengkapan.id}" type="checkbox" value="${kelengkapan.id}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="kelengkapan_second${kelengkapan.id}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">${kelengkapan.kelengkapan}</label>
                        </div>
                        <div class="relative z-0 w-full mb-6 group">
                            <input type="number" name="quantity_second[]" id="quantity_second${kelengkapan.id}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" required>
                            <label for="quantity_second${kelengkapan.id}" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Quantity</label>
                        </div>
                    </div>
                    `;
                });
            });
        } else {
            container.innerHTML = '';
        }
    });
});
