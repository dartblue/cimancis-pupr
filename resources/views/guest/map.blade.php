<x-cartrack-layout>
    <div class="flex h-screen" x-data="trackingMap()" x-init="initPageCartrack()">
        <!-- Sidebar -->
        <aside class="w-96 h-full bg-white border-r p-4 overflow-y-auto">
            <x-text-input class="mb-4" id="vehilce-search" x-model="searchQuery" placeholder="Cari kendaraan..." />
            <div class="flex">
                <div class="text-sm text-gray-600" x-text="vehicles.length + ' kendaraan'"></div>
            </div>
            <ul class="space-y-3 border-t mt-4">
                <template x-for="vehicle in filteredVehicles" :key="vehicle.vehicle_id">
                    <li class="p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer"
                        @click="showDetail(vehicle)" @hover="alert('hover')">
                        <div class="font-semibold"
                            x-text="vehicle.heavy_equipment.length > 0 ? vehicle.heavy_equipment[0].name : vehicle.manufacturer + ' ' +vehicle.model + ' ' + vehicle.model_year + ' ' + vehicle.colour">
                        </div>
                        <div class="text-xs text-gray-600" x-text="formatLatLon(vehicle)"></div>
                    </li>
                </template>
            </ul>
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
        <div id="map" class="w-full h-full"></div>

        <!-- Detail Tambahan di Bawah Map -->
        <div x-show="detailVehicle" x-transition
            class="fixed right-0 bottom-0 z-[1000] bg-white border-t shadow-lg p-6  mx-auto rounded-t-lg"
            style="display: none;">
            <h3 class="font-bold text-lg mb-2">Detail Tambahan Kendaraan</h3>
            {{-- Silakan isi detail tambahan di sini --}}
            <div>
                <span class="font-semibold">Nomor Polisi:</span>
                <span x-text="detailVehicle?.registration"></span>
            </div>
            <div>
                <span class="font-semibold">Merk & Model:</span>
                <span x-text="detailVehicle?.manufacturer + ' ' + detailVehicle?.model"></span>
            </div>
            <!-- Tambahkan detail lain sesuai kebutuhan -->
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
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            function trackingMap() {
                return {
                    map: null,
                    vehicles: [],
                    detailVehicle: null,
                    currentVehicle: null,
                    markers: {},
                    polylines: {},
                    searchQuery: '',
                    tab: 'semua',
                    startDate: new Date().setDate(new Date().getDate() - 7),
                    endDate: new Date(),

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

                    // initPageCartrack() {
                    //     this.initMap();

                    //     // inisialisasi flatpickr
                    //     flatpickr("#dateRangeInput", {
                    //         mode: "range",
                    //         dateFormat: "Y-m-d",
                    //         defaultDate: [
                    //             this.startDate,
                    //             this.endDate
                    //         ],
                    //         onChange: function(selectedDates, dateStr, instance) {
                    //             // Kalau sudah pilih 2 tanggal (start & end)
                    //             if (selectedDates.length === 2) {
                    //                 const startDate = selectedDates[0].toISOString().split("T")[0];
                    //                 const endDate = selectedDates[1].toISOString().split("T")[0];

                    //                 // alert(`Kamu memilih range:\nStart: ${startDate}\nEnd: ${endDate}`);
                    //                 console.log(
                    //                     `Kamu memilih range:\nStart: ${startDate}\nEnd: ${endDate}\nVehicle: ${this.detailVehicle?.vehicle_id}`
                    //                 );

                    //             }
                    //         }
                    //     });
                    // },
                    initPageCartrack() {
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
                                    // Update properti startDate dan endDate
                                    this.startDate = selectedDates[0];
                                    this.endDate = selectedDates[1];

                                    const startDate = selectedDates[0].toISOString().split("T")[0];
                                    const endDate = selectedDates[1].toISOString().split("T")[0];

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

                        this.loadData();
                    },

                    async loadData() {
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

                            // marker
                            if (!this.markers[v.vehicle_id]) {
                                this.markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                        icon: this.getCarIcon()
                                    }).addTo(this.map)
                                    .bindPopup(
                                        `<b>${v.heavy_equipment.length > 0 ? v.heavy_equipment[0].name : v.manufacturer}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`
                                    )
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            } else {
                                this.markers[v.vehicle_id].setLatLng([lastLat, lastLon])
                                    .setPopupContent(
                                        `<b>${v.heavy_equipment.length > 0 ? v.heavy_equipment[0].name : v.manufacturer}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`
                                    )
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            }
                        });
                    },

                    // async showDetail(vehicle) {
                    //     this.tab = 'semua';

                    //     // Format ke YYYY-MM-DD (misalnya untuk query param API)
                    //     const formatDate = (date) => {
                    //         const year = date.getFullYear();
                    //         const month = String(date.getMonth() + 1).padStart(2, '0');
                    //         const day = String(date.getDate()).padStart(2, '0');

                    //         return `${year}-${month}-${day}`;
                    //     };

                    //     const params = {
                    //         startDate: formatDate(new Date(this.startDate)),
                    //         endDate: formatDate(new Date(this.endDate)),
                    //     };

                    //     try {

                    //         const res = await fetch('/api/cartrack-activities', {
                    //             method: 'POST',
                    //             headers: {
                    //                 'Content-Type': 'application/json',
                    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    //                     'content')
                    //             },
                    //             body: JSON.stringify({
                    //                 vehicleId: vehicle.vehicle_id,
                    //                 ...params
                    //             })
                    //         });

                    //         const data = await res.json();
                    //         this.detailVehicle = data;
                    //         console.log(data);

                    //         // hapus semua polyline lama
                    //         for (const vehicleId in this.polylines) {
                    //             if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                    //         }

                    //         // buat polyline perjalanan kendaraan yang dipilih
                    //         const coords = [];
                    //         data.forEach(p => {
                    //             if (p.start_coordinates_latitude && p.start_coordinates_longitude) {
                    //                 coords.push([parseFloat(p.start_coordinates_latitude), parseFloat(p
                    //                     .start_coordinates_longitude)]);
                    //             }
                    //             if (p.end_coordinates_latitude && p.end_coordinates_longitude) {
                    //                 coords.push([parseFloat(p.end_coordinates_latitude), parseFloat(p
                    //                     .end_coordinates_longitude)]);
                    //             }
                    //         });

                    //         if (coords.length > 0) {
                    //             const color = this.getColorForVehicle(vehicle.vehicle_id);
                    //             this.polylines[vehicle.vehicle_id] = L.polyline(coords, {
                    //                 color: color,
                    //                 weight: 3
                    //             }).addTo(this.map);

                    //             const last = coords[coords.length - 1];
                    //             this.map.setView(last, 15, {
                    //                 animate: true
                    //             });
                    //         }

                    //     } catch (error) {
                    //         console.log(error);
                    //     }
                    // },

                    async showDetail(vehicle) {
                        this.currentVehicle = vehicle; // Simpan vehicle yang aktif
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
                    },

                    formatLatLon(v) {
                        if (!v.latest_activity) return "";

                        return ` ${v.latest_activity.end_location}`;
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
