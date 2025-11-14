<x-cartrack-layout>
    <div class="flex h-screen" x-data="trackingMap()" x-init="initPageCartrack()">
        <!-- Sidebar -->

        <aside class="w-96 h-full bg-white border-r overflow-y-auto">
            <!-- Sticky Header -->
            <div class="sticky top-0 bg-white z-10 p-4 border-b">
                <!-- Nav Tabs -->
                <div class="flex justify-between mb-4">
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="asideTab === 'proyek' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="asideTab = 'proyek'; loadDataProjects()">
                        Proyek
                    </button>
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="asideTab === 'kendaraan' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="asideTab = 'kendaraan'; loadData()">
                        Kendaraan
                    </button>
                </div>
                <!-- End Nav Tabs -->

                <!-- Tab Proyek Header -->
                <template x-if="asideTab === 'proyek'">
                    <div>
                        <h2 class="font-semibold text-lg mb-4">Daftar Proyek</h2>
                        <select id="year" name="year" x-model="selectedYear"
                            class="rounded-md mb-4 border-gray-300 w-full shadow-sm sm:text-sm focus:border-primary-500 focus:ring focus:ring-primary-500">
                            <template x-for="year in years" :key="year">
                                <option :value="year" x-text="year"></option>
                            </template>
                        </select>
                        <x-text-input class="mb-4" id="project-search" x-model="searchQueryProject"
                            placeholder="Cari proyek atau lokasi..." />
                    </div>
                </template>

                <!-- Tab Kendaraan Header -->
                <template x-if="asideTab === 'kendaraan'">
                    <div>
                        <h2 class="font-semibold text-lg mb-4">Daftar Kendaraan</h2>
                        <x-text-input class="mb-4" id="vehilce-search" x-model="searchQuery"
                            placeholder="Cari kendaraan..." />
                        <div class="flex">
                            <div class="text-sm text-gray-600" x-text="vehicles.length + ' kendaraan'"></div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- End Sticky Header -->

            <!-- Scrollable Content -->
            <div class="p-4">
                <template x-if="asideTab === 'proyek'">
                    <div id="project-list">
                        <template x-if="projects.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Tidak ada proyek ditemukan</p>
                            </div>
                        </template>
                        <template x-for="project in projects" :key="project.id">
                            <div class="mb-4 p-4 bg-white rounded shadow flex hover:shadow-lg transition cursor-pointer"
                                @click="
                            map.setView([parseFloat(project.latitude), parseFloat(project.longitude)], 15, { animate: true });
                            if (projectMarkers[project.id]) {
                                projectMarkers[project.id].openPopup();
                            }
                        ">
                                <div class="flex-shrink-0 mr-4">
                                    <template x-if="project.image_url">
                                        <img :src="project.image_url" :alt="project.project_name"
                                            class="w-24 h-24 object-cover rounded">
                                    </template>
                                    <template x-if="!project.image_url">
                                        <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-semibold"
                                        x-text="project.project_name || 'Nama Proyek Tidak Tersedia'"></p>
                                    <p class="text-sm text-gray-600"
                                        x-text="project.address || 'Alamat Tidak Tersedia'"></p>
                                    <p class="text-sm text-gray-600">
                                        <span x-text="project.village_name"></span>,
                                        <span x-text="project.district_name"></span>,
                                        <span x-text="project.city_name"></span>
                                    </p>
                                    <p class="text-sm">
                                        <span>Status: </span>
                                        <span class="font-semibold"
                                            :class="project.status === 'Selesai' ? 'text-green-600' : 'text-orange-600'"
                                            x-text="project.status || 'Status Tidak Tersedia'"></span>
                                    </p>
                                    <p class="text-sm"
                                        x-text="'Tipe: ' + (project.project_type || 'Tipe Tidak Tersedia')"></p>
                                    <template x-if="project.documentation_link">
                                        <p class="text-sm mt-2">
                                            <a :href="project.documentation_link" target="_blank"
                                                class="text-blue-500 hover:underline" @click.stop>Lihat Dokumentasi</a>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="asideTab === 'kendaraan'">
                    <ul class="space-y-3">
                        <template x-for="vehicle in filteredVehicles" :key="vehicle.vehicle_id">
                            <li class="p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer"
                                @click="showDetail(vehicle)">
                                <div class="font-semibold"
                                    x-text="vehicle.heavy_equipment.length > 0 ? vehicle.heavy_equipment[0].name : vehicle.manufacturer + ' ' +vehicle.model + ' ' + vehicle.model_year + ' ' + vehicle.colour">
                                </div>
                                <div class="text-xs text-gray-600" x-text="formatLatLon(vehicle)"></div>
                            </li>
                        </template>
                    </ul>
                </template>
            </div>
            <!-- End Scrollable Content -->
        </aside>

        <!-- Detail Sidebar -->
        <aside class="hidden w-96 h-full bg-white border-r p-2 overflow-y-auto" :class="{ 'hidden': !detailVehicle }">
            <div class="p-2 border-b flex justify-between items-center">
                <h2 class="font-semibold text-md">Detail Kendaraan</h2>
                <button class="text-slate-500 hover:text-slate-700"
                    @click="detailVehicle = null; currentVehicle = null">✕</button>
            </div>
            <div class="flex flex-col border-b p-2">
                <span x-text="currentVehicle?.registration"></span>
                <span class="text-xs"
                    x-text="currentVehicle?.manufacturer + ' ' + currentVehicle?.model + ' ' + currentVehicle?.model_year + ' ' + currentVehicle?.colour"></span>
            </div>
            <div class="">
                <h3 class="font-semibold text-md p-2 border-b">Aktivitas</h3>

                <!-- Date Range Picker -->
                <x-text-input id="dateRangeInput" placeholder="Periode" class="mt-2" />

                <!-- Nav Tabs -->
                <div class="flex justify-between mt-2 mb-2 border-b pb-2">
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'semua' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'semua'">
                        Semua
                    </button>
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'berjalan' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'berjalan'">
                        Berjalan
                    </button>
                </div>
                <!-- End Nav Tabs -->

                <!-- Tab Content -->
                <div class="mt-2">
                    <template x-if="tab === 'semua'">
                        <ul class="space-y-2">
                            <template x-for="pos in detailVehicle" :key="pos.id">
                                <li class="border rounded p-2 bg-gray-50">
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Trip ID:</span>
                                        <span x-text="pos.trip_id"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Dari:</span>
                                        <span x-text="pos.start_location || '-'"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Ke:</span>
                                        <span x-text="pos.end_location || '-'"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Waktu Mulai:</span>
                                        <span
                                            x-text="pos.start_timestamp ? new Date(pos.start_timestamp).toLocaleString() : '-'"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Waktu Selesai:</span>
                                        <span
                                            x-text="pos.end_timestamp ? new Date(pos.end_timestamp).toLocaleString() : '-'"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Jarak:</span>
                                        <span x-text="pos.trip_distance || 0"></span> km
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Durasi:</span>
                                        <span x-text="pos.trip_duration || '-'"></span>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="tab === 'berjalan'">
                        <div>
                            <div class="mb-4 flex gap-2 flex-wrap">
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Trip</div>
                                    <div class="text-lg" x-text="getOngoingTrips().length"></div>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Jarak</div>
                                    <div class="text-lg" x-text="getTotalDistance(getOngoingTrips())"></div>
                                    <span class="text-xs font-normal">km</span>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Waktu</div>
                                    <div class="text-lg" x-text="getTotalDuration(getOngoingTrips())"></div>
                                </div>
                            </div>
                            <ul class="space-y-2">
                                <template x-for="pos in getOngoingTrips()" :key="pos.id">
                                    <li class="border rounded p-2 bg-yellow-50">
                                        <div class="font-semibold text-yellow-700">Sedang Berjalan</div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Trip ID:</span>
                                            <span x-text="pos.trip_id"></span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Dari:</span>
                                            <span x-text="pos.start_location || '-'"></span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Waktu Mulai:</span>
                                            <span
                                                x-text="pos.start_timestamp ? new Date(pos.start_timestamp).toLocaleString() : '-'"></span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Jarak:</span>
                                            <span x-text="pos.trip_distance || 0"></span> km
                                        </div>
                                    </li>
                                </template>
                                <template x-if="getOngoingTrips().length === 0">
                                    <li class="text-xs text-gray-400 p-2">Tidak ada perjalanan berjalan.</li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
                <!-- End Tab Content -->
                {{-- <ul class="space-y-2 border-t mt-2">
                    <template x-for="pos in detailVehicle?.positions" :key="pos.id">
                        <li class="border rounded p-2 bg-gray-50">
                            <div class="text-xs text-gray-600"
                                x-text="'Dari: ' + (pos.start_latitude ? pos.start_latitude + ', ' + pos.start_longitude : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Ke: ' + (pos.end_latitude ? pos.end_latitude + ', ' + pos.end_longitude : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Waktu Mulai: ' + (pos.start_time ? new Date(pos.start_time).toLocaleString() : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Waktu Selesai: ' + (pos.end_time ? new Date(pos.end_time).toLocaleString() : '-')">
                            </div>
                        </li>
                    </template>
                </ul> --}}
            </div>
        </aside>

        <!-- Map -->
        <div id="map" class="w-full h-full">
            <!-- Loading Overlay -->
            <div x-show="isLoading" x-transition
                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-[9999]"
                style="display: none;">
                <div class="text-center">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-600 font-semibold">Memuat data...</p>
                </div>
            </div>
        </div>

        <!-- Detail Tambahan di Bawah Map -->
        <div x-show="detailVehicle" x-transition class="fixed bottom-0 z-[1000] bg-white border-t shadow-lg p-6"
            :style="`left: ${detailVehicle ? '768px' : '384px'}; right: 0;`" style="display: none;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg">Monitoring Kendaraan -
                    <span
                        x-text="new Date(startDate).toLocaleDateString('id-ID') + ' - ' + new Date(endDate).toLocaleDateString('id-ID')"></span>
                </h3>
                <button @click="detailVehicle = null; currentVehicle = null; destroyVehicleChart();"
                    class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="h-64">
                    <canvas id="vehicleMonitoringChart"></canvas>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-600">PTO Average</div>
                        <div class="text-lg font-semibold text-green-600" x-text="ptoAverage"></div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600">Battery Average</div>
                        <div class="text-lg font-semibold text-blue-600" x-text="batteryAverage"></div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600">Fuel Average</div>
                        <div class="text-lg font-semibold text-orange-600" x-text="fuelAverage"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Detail Tambahan -->
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            .custom-div-icon {
                background: transparent;
                border: none;
            }

            .marker-pin {
                width: 30px;
                height: 30px;
                border-radius: 50% 50% 50% 0;
                position: absolute;
                transform: rotate(-45deg);
                left: 50%;
                top: 50%;
                margin: -15px 0 0 -15px;
            }

            .marker-pin::after {
                content: '';
                width: 24px;
                height: 24px;
                margin: 3px 0 0 3px;
                background: #fff;
                position: absolute;
                border-radius: 50%;
            }

            .custom-div-icon i {
                position: absolute;
                width: 22px;
                font-size: 14px;
                left: 0;
                right: 0;
                margin: 10px auto;
                text-align: center;
            }

            .marker-pin.completed {
                background: #4CAF50;
            }

            .marker-pin.ongoing {
                background: #FFA500;
            }

            .custom-div-icon i.fa-check {
                color: #4CAF50;
            }

            .custom-div-icon i.fa-clock {
                color: #FFA500;
            }

            .leaflet-control a {
                background-color: #fff;
                border-bottom: 1px solid #ccc;
                width: 26px;
                height: 26px;
                line-height: 26px;
                display: block;
                text-align: center;
                text-decoration: none;
                color: black;
            }

            .leaflet-control a:hover {
                background-color: #f4f4f4;
            }

            .leaflet-control a:first-child {
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
            }

            .leaflet-control a:last-child {
                border-bottom-left-radius: 4px;
                border-bottom-right-radius: 4px;
                border-bottom: none;
            }

            .project-popup {
                font-family: Arial, sans-serif;
                display: flex;
                max-width: 280px;
            }

            .project-info {
                flex: 1;
                padding-right: 10px;
            }

            .project-title {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333 !important;
            }

            .project-details p {
                font-size: 11px;
                margin: 0 0 2px 0 !important;
                color: #666 !important;
            }

            .project-links {
                display: flex;
                flex-direction: column;
                margin-top: 5px;
            }

            .project-links a {
                text-decoration: none;
                color: #fff !important;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 11px;
                margin-bottom: 3px;
                text-align: center;
                transition: background-color 0.3s;
            }

            .doc-link {
                background-color: #030F6B !important;
            }

            .doc-link:hover {
                background-color: #fd7e14 !important;
            }

            .direction-link {
                background-color: #38a169 !important;
            }

            .direction-link:hover {
                background-color: #2f855a !important;
            }

            .project-image-container {
                width: 125px;
                height: 125px;
                overflow: hidden;
                border-radius: 3px;
            }

            .project-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .no-image {
                background-color: #f1f1f1;
                color: #999;
                text-align: center;
                padding: 10px;
                border-radius: 3px;
                font-size: 11px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .leaflet-popup-content-wrapper {
                padding: 0;
            }

            .leaflet-popup-content {
                margin: 0;
                padding: 20px;
            }

            /* Responsive styles for mobile */
            @media (max-width: 480px) {
                .leaflet-popup-content {
                    max-width: 250px;
                }

                .project-popup {
                    max-width: 240px;
                }

                .project-title {
                    font-size: 13px;
                }

                .project-details p {
                    font-size: 10px;
                }

                .project-links a {
                    font-size: 10px;
                    padding: 3px 6px;
                }

                .project-image-container {
                    width: 100px;
                    height: 100px;
                }
            }

            .user-location-icon {
                font-size: 30px;
                text-align: center;
                line-height: 40px;
            }

            .leaflet-control-attribution {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function trackingMap() {
                return {
                    map: null,
                    vehicles: [],
                    detailVehicle: null,
                    currentVehicle: null,
                    markers: {},
                    projectMarkers: {},
                    polylines: {},
                    searchQuery: '',
                    asideTab: 'proyek',
                    tab: 'semua',
                    startDate: new Date().setDate(new Date().getDate() - 7),
                    endDate: new Date(),
                    years: [],
                    selectedYear: new Date().getFullYear(),
                    projects: [],
                    searchQueryProject: '',
                    isLoading: false, // Tambahkan ini
                    vehicleMonitoringChart: null,
                    ptoAverage: '0%',
                    batteryAverage: '0%',
                    fuelAverage: '0%',

                    init() {
                        // Watch untuk searchQueryProject
                        this.$watch('searchQueryProject', (value) => {
                            if (value.length >= 3 || value.length === 0) {
                                this.searchProjects();
                            }
                        });

                        // Watch untuk selectedYear
                        this.$watch('selectedYear', (value) => {
                            if (this.asideTab === 'proyek') {
                                this.searchProjects();
                            }
                        });
                    },
                    initVehicleMonitoringChart() {
                        const ctx = document.getElementById('vehicleMonitoringChart');
                        if (!ctx) return;

                        // Destroy existing chart if any
                        if (this.vehicleMonitoringChart) {
                            this.vehicleMonitoringChart.destroy();
                        }

                        // Generate dummy data berdasarkan range tanggal
                        const labels = [];
                        const ptoData = [];
                        const batteryData = [];
                        const fuelData = [];

                        const start = new Date(this.startDate);
                        const end = new Date(this.endDate);
                        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                        // Generate data per hari
                        for (let i = 0; i <= diffDays; i++) {
                            const date = new Date(start);
                            date.setDate(date.getDate() + i);
                            labels.push(date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short'
                            }));

                            // Dummy data dengan variasi realistis
                            const ptoValue = Math.random() > 0.3 ? 100 : 0; // 70% aktif
                            const batteryValue = 75 + Math.random() * 20; // 75-95%
                            const fuelValue = 50 + Math.random() * 40 - (i * 2); // berkurang seiring waktu

                            ptoData.push(ptoValue);
                            batteryData.push(batteryValue.toFixed(1));
                            fuelData.push(Math.max(20, fuelValue).toFixed(1));
                        }

                        // Hitung average
                        const ptoAvg = ptoData.filter(v => v > 0).length / ptoData.length * 100;
                        const batteryAvg = batteryData.reduce((sum, v) => sum + parseFloat(v), 0) / batteryData.length;
                        const fuelAvg = fuelData.reduce((sum, v) => sum + parseFloat(v), 0) / fuelData.length;

                        this.ptoAverage = ptoAvg.toFixed(0) + '% Active';
                        this.batteryAverage = batteryAvg.toFixed(1) + '%';
                        this.fuelAverage = fuelAvg.toFixed(1) + '%';

                        this.vehicleMonitoringChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'PTO Status (%)',
                                        data: ptoData,
                                        borderColor: '#10B981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 0.1,
                                        yAxisID: 'y',
                                        hidden: false
                                    },
                                    {
                                        label: 'Battery Level (%)',
                                        data: batteryData,
                                        borderColor: '#3B82F6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 0.4,
                                        yAxisID: 'y',
                                        hidden: false
                                    },
                                    {
                                        label: 'Fuel Level (%)',
                                        data: fuelData,
                                        borderColor: '#F59E0B',
                                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 0.4,
                                        yAxisID: 'y',
                                        hidden: false
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15,
                                            font: {
                                                size: 12
                                            }
                                        },
                                        onClick: (e, legendItem, legend) => {
                                            const index = legendItem.datasetIndex;
                                            const chart = legend.chart;
                                            const meta = chart.getDatasetMeta(index);

                                            // Toggle visibility
                                            meta.hidden = meta.hidden === null ? !chart.data.datasets[index]
                                                .hidden : null;
                                            chart.update();
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    if (context.datasetIndex === 0) {
                                                        // PTO
                                                        label += context.parsed.y > 0 ? 'Active (100%)' :
                                                            'Inactive (0%)';
                                                    } else {
                                                        label += context.parsed.y + '%';
                                                    }
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        type: 'linear',
                                        display: true,
                                        position: 'left',
                                        min: 0,
                                        max: 100,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '%';
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Percentage (%)'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                }
                            }
                        });
                    },

                    destroyVehicleChart() {
                        if (this.vehicleMonitoringChart) {
                            this.vehicleMonitoringChart.destroy();
                            this.vehicleMonitoringChart = null;
                        }
                    },



                    async searchProjects() {
                        this.isLoading = true;

                        // Hapus semua marker proyek
                        this.map.eachLayer((layer) => {
                            if (layer instanceof L.Marker) {
                                this.map.removeLayer(layer);
                            }
                        });

                        // Reset projectMarkers
                        this.projectMarkers = {};

                        try {
                            const params = new URLSearchParams({
                                year: this.selectedYear,
                                query: this.searchQueryProject
                            });

                            const res = await fetch(`/api/projects?${params.toString()}`);
                            this.projects = await res.json();

                            console.log('Search results:', this.projects);

                            // Tambahkan marker untuk hasil pencarian
                            this.projects.forEach(proj => {
                                if (!proj.latitude || !proj.longitude) return;

                                const lat = parseFloat(proj.latitude);
                                const lon = parseFloat(proj.longitude);

                                // Simpan marker ke projectMarkers
                                this.projectMarkers[proj.id] = L.marker([lat, lon], {
                                        icon: this.getProjectIcon(proj.status)
                                    }).addTo(this.map)
                                    .bindPopup(this.getPopupContenForProject(proj));
                            });

                            // Jika ada hasil, zoom ke proyek pertama
                            if (this.projects.length > 0 && this.projects[0].latitude && this.projects[0].longitude) {
                                this.map.setView([
                                    parseFloat(this.projects[0].latitude),
                                    parseFloat(this.projects[0].longitude)
                                ], 13);
                            }
                        } catch (error) {
                            console.error('Error searching projects:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },
                    get filteredVehicles() {
                        if (!this.searchQuery) return this.vehicles;
                        const q = this.searchQuery.toLowerCase();

                        return this.vehicles.filter(v => {
                            // gabungkan semua field yang relevan untuk pencarian
                            const searchable = [
                                v.registration,
                                v.vehicle_id,
                                v.manufacturer,
                                v.model,
                                v.model_year,
                                v.colour,
                                v.heavy_equipment?.length > 0 ? v.heavy_equipment[0].name : null
                            ].filter(Boolean).join(" ").toLowerCase();

                            return searchable.includes(q);
                        });
                    },

                    totalDistance(positions) {
                        // Asumsi setiap posisi punya properti distance_km
                        if (!positions) return 0;
                        return positions.reduce((sum, p) => sum + (parseFloat(p.distance_km) || 0), 0).toFixed(2);
                    },

                    totalDuration(positions) {
                        // Hitung total waktu dari start_time sampai sekarang (karena belum selesai)
                        if (!positions) return '0 menit';
                        let totalMs = 0;
                        positions.forEach(p => {
                            if (p.start_time) {
                                const start = new Date(p.start_time);
                                const end = new Date(); // sekarang
                                totalMs += end - start;
                            }
                        });
                        // Konversi ms ke jam dan menit
                        const totalMinutes = Math.floor(totalMs / 60000);
                        const hours = Math.floor(totalMinutes / 60);
                        const minutes = totalMinutes % 60;
                        if (hours > 0) {
                            return `${hours} jam ${minutes} menit`;
                        }
                        return `${minutes} menit`;
                    },

                    async getYears() {
                        const res = await fetch('/api/project-years');
                        this.years = await res.json();
                    },

                    initPageCartrack() {
                        this.getYears();
                        this.initMap();

                        // inisialisasi flatpickr
                        flatpickr("#dateRangeInput", {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            defaultDate: [
                                this.startDate,
                                this.endDate
                            ],
                            onChange: (selectedDates, dateStr, instance) => {
                                // Kalau sudah pilih 2 tanggal (start & end)
                                if (selectedDates.length === 2) {
                                    const start = selectedDates[0];
                                    const end = selectedDates[1];

                                    // Hitung selisih hari
                                    const diffTime = Math.abs(end - start);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                                    // Validasi maksimal 9 hari
                                    if (diffDays > 9) {
                                        alert('Rentang tanggal maksimal 9 hari!');

                                        // Reset ke default (7 hari terakhir)
                                        const defaultEnd = new Date();
                                        const defaultStart = new Date();
                                        defaultStart.setDate(defaultStart.getDate() - 7);

                                        instance.setDate([defaultStart, defaultEnd]);
                                        return;
                                    }

                                    // Update properti startDate dan endDate
                                    this.startDate = start;
                                    this.endDate = end;

                                    const startDate = start.toISOString().split("T")[0];
                                    const endDate = end.toISOString().split("T")[0];

                                    console.log(`Range berubah: ${startDate} - ${endDate}`);

                                    // Jika ada vehicle yang sedang aktif, refresh detailnya
                                    if (this.currentVehicle) {
                                        this.showDetail(this.currentVehicle);
                                    }
                                }
                            }
                        });
                    },

                    initMap() {
                        this.map = L.map('map').setView([-6.733742, 108.530256], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(this.map);

                        // this.loadData();
                        this.loadDataProjects();
                    },

                    async loadDataProjects() {
                        this.detailVehicle = null;
                        this.currentVehicle = null;
                        this.searchQueryProject = ''; // Reset search
                        await this.searchProjects(); // Gunakan searchProjects untuk load data
                    },

                    async loadData() {
                        this.isLoading = true;

                        // Hapus SEMUA marker (proyek dan kendaraan)
                        this.map.eachLayer((layer) => {
                            if (layer instanceof L.Marker) {
                                this.map.removeLayer(layer);
                            }
                        });

                        // Hapus semua polyline
                        for (const vehicleId in this.polylines) {
                            if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                        }
                        this.polylines = {};

                        try {
                            const res = await fetch('/api/cartrack-vehicles');
                            this.vehicles = await res.json();

                            this.vehicles.forEach(v => {
                                if (!v.latest_activity) return;

                                const lastLat = parseFloat(v.latest_activity.end_coordinates_latitude ?? v
                                    .latest_activity
                                    .start_coordinates_latitude);
                                const lastLon = parseFloat(v.latest_activity.end_coordinates_longitude ?? v
                                    .latest_activity
                                    .start_coordinates_longitude);

                                // Buat marker baru setiap kali load
                                this.markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                        icon: this.getCarIcon()
                                    }).addTo(this.map)
                                    .bindPopup(
                                        `<b>${v.heavy_equipment.length > 0 ? v.heavy_equipment[0].name : v.manufacturer}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`
                                    )
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            });
                        } catch (error) {
                            console.error('Error loading vehicles:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async showDetail(vehicle) {
                        this.currentVehicle = vehicle;
                        this.tab = 'semua';

                        // Format ke YYYY-MM-DD
                        const formatDate = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        };

                        const params = {
                            startDate: formatDate(new Date(this.startDate)),
                            endDate: formatDate(new Date(this.endDate)),
                        };

                        try {
                            const res = await fetch('/api/cartrack-activities', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    vehicleId: vehicle.vehicle_id,
                                    ...params
                                })
                            });

                            const data = await res.json();
                            this.detailVehicle = data;
                            console.log('Data received:', data);

                            // hapus semua polyline lama
                            for (const vehicleId in this.polylines) {
                                if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                            }

                            // buat polyline perjalanan kendaraan yang dipilih
                            const coords = [];
                            data.forEach(p => {
                                if (p.start_coordinates_latitude && p.start_coordinates_longitude) {
                                    coords.push([parseFloat(p.start_coordinates_latitude), parseFloat(p
                                        .start_coordinates_longitude)]);
                                }
                                if (p.end_coordinates_latitude && p.end_coordinates_longitude) {
                                    coords.push([parseFloat(p.end_coordinates_latitude), parseFloat(p
                                        .end_coordinates_longitude)]);
                                }
                            });

                            console.log('Coordinates for polyline:', coords);

                            if (coords.length > 0) {
                                const color = this.getColorForVehicle(vehicle.vehicle_id);
                                this.polylines[vehicle.vehicle_id] = L.polyline(coords, {
                                    color: color,
                                    weight: 3
                                }).addTo(this.map);

                                const last = coords[coords.length - 1];
                                this.map.setView(last, 15, {
                                    animate: true
                                });
                            } else {
                                console.log('No valid coordinates found for polyline');
                            }

                        } catch (error) {
                            console.log('Error in showDetail:', error);
                        }

                        // Initialize chart AFTER detailVehicle is set (moved outside try-catch)
                        // Gunakan $nextTick untuk memastikan DOM sudah ready
                        this.$nextTick(() => {
                            setTimeout(() => {
                                console.log('Initializing chart...');
                                const canvas = document.getElementById('vehicleMonitoringChart');
                                console.log('Canvas element:', canvas);

                                if (canvas) {
                                    this.initVehicleMonitoringChart();
                                } else {
                                    console.error('Canvas element not found!');
                                }
                            }, 200); // Tambah delay untuk memastikan DOM ready
                        });
                    },

                    initVehicleMonitoringChart() {
                        const ctx = document.getElementById('vehicleMonitoringChart');
                        console.log('Chart init - Canvas element:', ctx);

                        if (!ctx) {
                            console.error('Canvas not found!');
                            return;
                        }

                        // Destroy existing chart if any
                        if (this.vehicleMonitoringChart) {
                            console.log('Destroying existing chart...');
                            this.vehicleMonitoringChart.destroy();
                        }

                        // Check if Chart.js is loaded
                        if (typeof Chart === 'undefined') {
                            console.error('Chart.js not loaded!');
                            return;
                        }

                        // Generate dummy data berdasarkan range tanggal
                        const labels = [];
                        const ptoData = [];
                        const batteryData = [];
                        const fuelData = [];

                        const start = new Date(this.startDate);
                        const end = new Date(this.endDate);
                        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                        console.log(`Generating data for ${diffDays} days`);

                        // Generate data per hari
                        for (let i = 0; i <= diffDays; i++) {
                            const date = new Date(start);
                            date.setDate(date.getDate() + i);
                            labels.push(date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short'
                            }));

                            // Dummy data dengan variasi realistis
                            const ptoValue = Math.random() > 0.3 ? 100 : 0; // 70% aktif
                            const batteryValue = 75 + Math.random() * 20; // 75-95%
                            const fuelValue = 50 + Math.random() * 40 - (i * 2); // berkurang seiring waktu

                            ptoData.push(ptoValue);
                            batteryData.push(parseFloat(batteryValue.toFixed(1)));
                            fuelData.push(parseFloat(Math.max(20, fuelValue).toFixed(1)));
                        }

                        console.log('Chart data:', {
                            labels,
                            ptoData,
                            batteryData,
                            fuelData
                        });

                        // Hitung average
                        const ptoAvg = ptoData.filter(v => v > 0).length / ptoData.length * 100;
                        const batteryAvg = batteryData.reduce((sum, v) => sum + v, 0) / batteryData.length;
                        const fuelAvg = fuelData.reduce((sum, v) => sum + v, 0) / fuelData.length;

                        this.ptoAverage = ptoAvg.toFixed(0) + '% Active';
                        this.batteryAverage = batteryAvg.toFixed(1) + '%';
                        this.fuelAverage = fuelAvg.toFixed(1) + '%';

                        try {
                            this.vehicleMonitoringChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                            label: 'PTO Status (%)',
                                            data: ptoData,
                                            borderColor: '#10B981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            borderWidth: 2,
                                            fill: false,
                                            tension: 0.1,
                                            yAxisID: 'y'
                                        },
                                        {
                                            label: 'Battery Level (%)',
                                            data: batteryData,
                                            borderColor: '#3B82F6',
                                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                            borderWidth: 2,
                                            fill: false,
                                            tension: 0.4,
                                            yAxisID: 'y'
                                        },
                                        {
                                            label: 'Fuel Level (%)',
                                            data: fuelData,
                                            borderColor: '#F59E0B',
                                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                            borderWidth: 2,
                                            fill: false,
                                            tension: 0.4,
                                            yAxisID: 'y'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                usePointStyle: true,
                                                padding: 15,
                                                font: {
                                                    size: 12
                                                }
                                            },
                                            onClick: (e, legendItem, legend) => {
                                                const index = legendItem.datasetIndex;
                                                const chart = legend.chart;
                                                const meta = chart.getDatasetMeta(index);

                                                meta.hidden = meta.hidden === null ? !chart.data.datasets[index]
                                                    .hidden : null;
                                                chart.update();
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.dataset.label || '';
                                                    if (label) {
                                                        label += ': ';
                                                    }
                                                    if (context.parsed.y !== null) {
                                                        if (context.datasetIndex === 0) {
                                                            label += context.parsed.y > 0 ? 'Active (100%)' :
                                                                'Inactive (0%)';
                                                        } else {
                                                            label += context.parsed.y.toFixed(1) + '%';
                                                        }
                                                    }
                                                    return label;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            type: 'linear',
                                            display: true,
                                            position: 'left',
                                            min: 0,
                                            max: 100,
                                            ticks: {
                                                callback: function(value) {
                                                    return value + '%';
                                                }
                                            },
                                            title: {
                                                display: true,
                                                text: 'Percentage (%)'
                                            }
                                        },
                                        x: {
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45
                                            }
                                        }
                                    }
                                }
                            });

                            console.log('Chart created successfully:', this.vehicleMonitoringChart);
                        } catch (error) {
                            console.error('Error creating chart:', error);
                        }
                    },

                    formatLatLon(v) {
                        if (!v.latest_activity) return "";

                        return ` ${v.latest_activity.end_location}`;
                    },

                    getPopupContenForProject(project) {
                        return `
                            <div class="project-popup">
                                <div class="project-info">
                                    <h3 class="project-title">${project.project_name}</h3>
                                    <div class="project-details">
                                        <p>${project.village_name}, ${project.district_name}</p>
                                        <p>${project.city_name}</p>
                                    </div>
                                    <div class="project-links">
                                        ${project.documentation_link ?
                                        `<a href="${project.documentation_link}" target="_blank" class="doc-link">Dokumentasi</a>` :
                                        ''}
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=${project.latitude},${project.longitude}" target="_blank" class="direction-link">Menuju Lokasi</a>
                                    </div>
                                </div>
                                <div class="project-image-container">
                                    ${project.image_url ?
                                        `<a href="${project.image_url}" data-fancybox="gallery" data-caption="${project.project_name}" class="project-image">
                                                                                                                                                                                                                                                                                                                                                    <img src="${project.image_url}" alt="${project.project_name}">
                                                                                                                                                                                                                                                                                                                                                </a>` :
                                        `<div class="no-image">Tidak ada gambar</div>`
                                    }
                                </div>
                            </div>
                        `;
                    },

                    getProjectIcon(status) {
                        if (status === 'Selesai') {
                            return this.getCompletedIcon();
                        } else {
                            return this.getOngoingIcon();
                        }
                    },

                    getCompletedIcon() {
                        return L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div class='marker-pin completed'></div><i class='fas fa-check'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });
                    },

                    getOngoingIcon() {
                        return L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div class='marker-pin ongoing'></div><i class='fas fa-clock'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });
                    },

                    getCarIcon() {
                        return L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div class='marker-pin completed'></div><i class='fas fa-check'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });
                    },


                    getColorForVehicle(vehicleId) {
                        const colors = [
                            "blue", "red", "green", "orange", "purple",
                            "brown", "pink", "black", "teal", "cyan"
                        ];
                        return colors[vehicleId % colors.length];
                    },
                    getOngoingTrips() {
                        if (!this.detailVehicle) return [];

                        // Untuk testing: tampilkan semua trip dalam range tanggal yang dipilih
                        console.log('All trips in date range:', this.detailVehicle);
                        return this.detailVehicle;

                        // Atau jika ingin filter trip dengan jarak > 0 (yang benar-benar bergerak)
                        // return this.detailVehicle.filter(trip => {
                        //     return parseFloat(trip.trip_distance) > 0;
                        // });
                    },

                    getTotalDistance(trips) {
                        if (!trips || trips.length === 0) return 0;
                        const total = trips.reduce((sum, trip) => {
                            const distance = parseFloat(trip.trip_distance) || 0;
                            console.log(`Trip ${trip.trip_id}: distance = ${distance}`);
                            return sum + distance;
                        }, 0);
                        console.log(`Total distance: ${total}`);
                        return total.toFixed(2);
                    },

                    getTotalDuration(trips) {
                        if (!trips || trips.length === 0) return '0 menit';

                        let totalSeconds = 0;
                        trips.forEach(trip => {
                            if (trip.trip_duration_seconds) {
                                const seconds = parseInt(trip.trip_duration_seconds) || 0;
                                console.log(`Trip ${trip.trip_id}: duration = ${seconds} seconds`);
                                totalSeconds += seconds;
                            }
                        });

                        console.log(`Total seconds: ${totalSeconds}`);
                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);

                        if (hours > 0) {
                            return `${hours} jam ${minutes} menit`;
                        }
                        return `${minutes} menit`;
                    },

                    totalDistance(positions) {
                        // Fungsi yang sudah ada, sesuaikan dengan struktur data baru
                        if (!positions) return 0;
                        return positions.reduce((sum, p) => sum + (parseFloat(p.trip_distance) || 0), 0).toFixed(2);
                    },

                    totalDuration(positions) {
                        // Fungsi yang sudah ada, sesuaikan dengan struktur data baru
                        if (!positions) return '0 menit';
                        let totalSeconds = 0;
                        positions.forEach(p => {
                            if (p.trip_duration_seconds) {
                                totalSeconds += parseInt(p.trip_duration_seconds);
                            }
                        });

                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);

                        if (hours > 0) {
                            return `${hours} jam ${minutes} menit`;
                        }
                        return `${minutes} menit`;
                    },
                }
            }
        </script>
    @endpush
</x-cartrack-layout>
