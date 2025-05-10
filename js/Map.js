const map = L.map('map').setView([0, 0], 13);
let allChargers = [];
let chargerMarkers = [];
let userPosition = null;

// Tile layer from OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Custom charger icon
const chargerIcon = L.icon({
    iconUrl: 'Leaflet/images/marker-icon.png',
    shadowUrl: 'Leaflet/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Get user's current location
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        position => {
            userPosition = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setView([userPosition.lat, userPosition.lng], 14);

            L.marker([userPosition.lat, userPosition.lng], { icon: chargerIcon })
                .addTo(map)
                .bindPopup("You are here!")
                .openPopup();
        },
        () => alert("Could not get your location.")
    );
}

// Fetch charger data from PHP
fetch('API/getChargerPoints.php')
    .then(response => response.json())
    .then(data => {
        allChargers = data;
        displayChargers(allChargers);
    })
    .catch(error => {
        console.error('Error loading charger data:', error);
    });

// Display markers for all chargers
function displayChargers(chargers) {
    // Clear previous markers
    chargerMarkers.forEach(marker => map.removeLayer(marker));
    chargerMarkers = [];

    chargers.forEach(point => {
        const lat = parseFloat(point.Latitude);
        const lng = parseFloat(point.Longitude);

        if (!isNaN(lat) && !isNaN(lng)) {
            const marker = L.marker([lat, lng], { icon: chargerIcon })
                .addTo(map)
                .bindPopup(`
                    <strong>ID:</strong> ${point.Charger_point_ID}<br>
                    <strong>Type:</strong> ${point.Connector_type}<br>
                    <strong>Price:</strong> ${point.Price_per_kWatt} BD
                `);

            marker.on('click', () => displayChargerDetails(point));
            chargerMarkers.push(marker);
        }
    });
}

// Inject charger details into Bootstrap card
function displayChargerDetails(point) {
    const detailsCard = document.getElementById('charger-info');
    const message = document.getElementById('select-message');

    if (message) {
        message.style.display = 'none';
    }

    detailsCard.innerHTML = `
        <h5 class="mb-3">Charger ID: ${point.Charger_point_ID}</h5>
        <ul class="card-details">
            <li><strong>Description:</strong> ${point.Charger_point_description}</li>
            <li><strong>Price per kWatt:</strong> ${point.Price_per_kWatt} BD</li>
            <li><strong>Connector Type:</strong> ${point.Connector_type}</li>
            <li><strong>Rating:</strong> ${point.Rating} / 5</li>
        </ul>
    `;
}