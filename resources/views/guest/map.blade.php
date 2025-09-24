<x-cartrack-layout>
    <div class="flex h-screen" x-data="trackingMap()" x-init="initMap()">
        <!-- Sidebar -->
        <aside class="w-64 h-full bg-white border-r p-4 overflow-y-auto">
            <x-text-input class="mb-4" />
            <ul class="space-y-3">
                <template x-for="vehicle in vehicles" :key="vehicle.vehicle_id">
                    <li class="p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer"
                        @click="showDetail(vehicle)">
                        <div class="font-semibold" x-text="vehicle.registration"></div>
                        <div class="text-xs text-gray-600" x-text="formatLatLon(vehicle)"></div>
                    </li>
                </template>
            </ul>
        </aside>

        <!-- Detail Sidebar -->
        <aside class="hidden w-64 h-full bg-white border-r p-4 overflow-y-auto" :class="{ 'hidden': !detailVehicle }">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-lg">Detail Kendaraan</h2>
                <button class="text-slate-500 hover:text-slate-700" @click="detailVehicle = null">✕</button>
            </div>
            <div class="flex border-b p-2">
                <span x-text="detailVehicle?.registration"></span>
            </div>
        </aside>

        <!-- Map -->
        <div id="map" class="w-full h-full"></div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            function trackingMap() {
                return {
                    map: null,
                    vehicles: [],
                    detailVehicle: null,
                    markers: {},
                    polylines: {},

                    initMap() {
                        this.map = L.map('map').setView([-6.200000, 106.816666], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(this.map);

                        this.loadData();
                    },

                    async loadData() {
                        const res = await fetch('/tracking/data');
                        this.vehicles = await res.json();

                        this.vehicles.forEach(v => {
                            if (!v.positions?.length) return;

                            const last = v.positions[v.positions.length - 1];
                            const lastLat = parseFloat(last.end_latitude ?? last.start_latitude);
                            const lastLon = parseFloat(last.end_longitude ?? last.start_longitude);

                            // marker
                            if (!this.markers[v.vehicle_id]) {
                                this.markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                        icon: this.getCarIcon()
                                    }).addTo(this.map)
                                    .bindPopup(`<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                            } else {
                                this.markers[v.vehicle_id].setLatLng([lastLat, lastLon])
                                    .setPopupContent(
                                        `<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                            }
                        });
                    },

                    showDetail(vehicle) {
                        this.detailVehicle = vehicle;

                        // hapus semua polyline lama
                        for (const vehicleId in this.polylines) {
                            if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                        }

                        // buat polyline perjalanan kendaraan yang dipilih
                        const coords = [];
                        vehicle.positions.forEach(p => {
                            if (p.start_latitude && p.start_longitude) {
                                coords.push([parseFloat(p.start_latitude), parseFloat(p.start_longitude)]);
                            }
                            if (p.end_latitude && p.end_longitude) {
                                coords.push([parseFloat(p.end_latitude), parseFloat(p.end_longitude)]);
                            }
                        });

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
                        }
                    },

                    formatLatLon(v) {
                        if (!v.positions?.length) return "";
                        const last = v.positions[v.positions.length - 1];
                        const lat = parseFloat(last.end_latitude ?? last.start_latitude);
                        const lon = parseFloat(last.end_longitude ?? last.start_longitude);
                        return `Lat: ${lat.toFixed(5)} | Lon: ${lon.toFixed(5)}`;
                    },

                    getCarIcon() {
                        return L.icon({
                            iconUrl: "https://cdn-icons-png.flaticon.com/512/61/61168.png",
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                            popupAnchor: [0, -16]
                        });
                    },

                    getColorForVehicle(vehicleId) {
                        const colors = [
                            "blue", "red", "green", "orange", "purple",
                            "brown", "pink", "black", "teal", "cyan"
                        ];
                        return colors[vehicleId % colors.length];
                    }
                }
            }
        </script>
    @endpush
</x-cartrack-layout>
