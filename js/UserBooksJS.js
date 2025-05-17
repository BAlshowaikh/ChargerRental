function loadChargerDetails(chargerId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "getChargerDetails.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const charger = JSON.parse(xhr.responseText);

            // Remove existing popup if present
            const existingPopup = document.getElementById('popup');
            if (existingPopup) existingPopup.remove();

            // Create popup overlay
            const popup = document.createElement('div');
            popup.className = 'popup-overlay';
            popup.id = 'popup';

            // Build popup content
            popup.innerHTML = `
                <div class="popup-card popup-large shadow">
                <button class="popup-close" onclick="document.getElementById('popup').remove()" >&times;</button>                
                    <div class="popup-content d-flex flex-wrap">
                        <!-- Map Section -->
                        <div class="popup-map" id="popup-map"></div>
                
                        <!-- Info Section -->
                        <div class="popup-info px-4 py-3" style="width: 100%; background-color: #f9f9f9;">
                            <h4 class="mb-3 fw-bold" style="color: #007bff; font-size: 1.4rem;">🔌 ${charger.name}</h4>
                            <p class="mb-2" style="font-size: 1rem;">
                                <strong style="color: #444;">Description:</strong><br>
                                <span style="color: #666;">${(charger.description || 'No description available').replace(/\n/g, '<br>')}</span>
                            </p>
                            <p class="mb-2" style="font-size: 1rem;">
                                <strong style="color: #444;">Connector Type:</strong>
                                <span class="badge bg-secondary" style="font-size: 0.95rem;">${charger.connector_type}</span>
                            </p>
                            <p style="font-size: 1rem;">
                                <strong style="color: #444;">Price per kWh:</strong>
                                <span style="color: #28a745; font-weight: 600;">${charger.price_per_kw} BD</span>
                            </p>
                        </div>
                    </div>
                </div>
                `;

            document.body.appendChild(popup);

            console.log('charger:', charger);

            // Initialize map
            setTimeout(() => {
                const lat = parseFloat(charger.lat);
                const lng = parseFloat(charger.lng);

                const map = L.map('popup-map').setView([lat, lng], 16);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Define custom icon if needed
                const chargerIcon = L.icon({
                    iconUrl: 'Leaflet/images/marker-icon.png',
                    shadowUrl: 'Leaflet/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                // Add a single charger marker
                L.marker([lat, lng], { icon: chargerIcon })
                    .addTo(map)
                    .bindPopup(`<div style="font-weight: 600; color: #333;">⚡ ${charger.name}</div>`)
                    .openPopup();
            }, 100);



        }
    };

    xhr.send("charger_id=" + encodeURIComponent(chargerId));
}

// Auto-submit filters dynamically
const filterForm = document.getElementById('filterForm');
document.getElementById('statusSelect').addEventListener('change', () => filterForm.submit());
document.getElementById('sortSelect').addEventListener('change', () => filterForm.submit());
