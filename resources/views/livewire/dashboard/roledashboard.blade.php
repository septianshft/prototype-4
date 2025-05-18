<div class="p-6 space-y-6">
    <h2 class="text-2xl font-bold">Dashboard {{ ucfirst(Auth::user()->role) }}</h2>

    {{-- Statistik Kartu --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @if (in_array(Auth::user()->role, ['admin', 'vicedirector']))
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-user  text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Data Penerima Beasiswa</p>
                    <h3 class="text-2xl font-bold">{{ $data['penerimaBeasiswa'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-user  text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Data Dosen Pembimbing</p>
                    <h3 class="text-2xl font-bold">{{ $data['jumlahDosen'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-file text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Beasiswa yang tersedia</p>
                    <h3 class="text-2xl font-bold">{{ $data['jumlahBeasiswa'] ?? 0 }}</h3>
                </div>
            </div>
        @elseif(Auth::user()->role === 'dosen')
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Penerima Beasiswa (Beasiswa Anda)</p>
                    <h3 class="text-2xl font-bold">{{ $data['penerimaBeasiswa'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mahasiswa Pending</p>
                    <h3 class="text-2xl font-bold">{{ $data['mahasiswaPending'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fa-solid fa-file text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Beasiswa yang Anda Kelola</p>
                    <h3 class="text-2xl font-bold">{{ $data['beasiswaKelola'] ?? 0 }}</h3>
                </div>
            </div>
        @else
            <p>Anda tidak memiliki akses dashboard khusus.</p>
        @endif
    </div>

    {{-- Chart --}}
    <div class="bg-white shadow-md rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Total Penerima Beasiswa Tiap Tahun</h3>
        <canvas id="monthlyChart" height="100"></canvas>
    </div>
</div>

{{-- Font Awesome CDN --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

{{-- ChartJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Jumlah Penerima Beasiswa per Tahun',
                data: {!! json_encode($chartData) !!},
                backgroundColor: '#1f2937',
                borderRadius: 6,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
