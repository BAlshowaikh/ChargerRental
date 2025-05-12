const map = L.map('map').setView([0, 0], 13);
let allChargers = [];
let chargerMarkers = [];
let userPosition = null;

// Tile layer from OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Custom user icon
const userIcon = L.icon({
    iconUrl: 'Leaflet/images/user-marker.png',
    shadowUrl: 'Leaflet/images/marker-shadow.png',
    iconSize: [50, 50],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

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

            L.marker([userPosition.lat, userPosition.lng], { icon: userIcon })
                .addTo(map)
                .bindPopup("You are here!")
                .openPopup();
        },
        () => alert("Could not get your location.")
    );
}

// Fetch charger data using XMLHttpRequest
const xhr = new XMLHttpRequest();
xhr.open('GET', '/SearchChargePoints.php?action=getChargers', true);
xhr.responseType = 'json';

xhr.onload = function () {
    if (xhr.status === 200) {
        const chargers = xhr.response;
        allChargers = chargers;
        displayChargers(allChargers);
    } else {
        console.error('Error loading charger data: HTTP', xhr.status);
    }
};

xhr.onerror = function () {
    console.error('Network error while loading charger data.');
};

xhr.send();


// The below code is map view
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

// Inject charger details for map view
function displayChargerDetails(point) {
    const detailsCard = document.getElementById('charger-info');
    const message = document.getElementById('select-message');
    const bookBtnContainer = document.getElementById('book-button-container');

    if (message) {
        message.style.display = 'none';
    }

    if (bookBtnContainer) {
        bookBtnContainer.classList.remove('d-none');
    }

    detailsCard.innerHTML = `
    <div class="d-flex flex-column h-100">
        <h5 class="mb-2">Charger ID: ${point.Charger_point_ID}</h5>
        <ul class="card-details mb-4">
            <li><strong>Name:</strong> ${point.Name}</li>
            <li><strong>Description:</strong> ${point.Charger_point_description}</li>
            <li><strong>Price per kWatt:</strong> ${point.Price_per_kWatt} BD</li>
            <li><strong>Availability:</strong> ${point.Availability_status}</li>
            <li><strong>Connector Type:</strong> ${point.Connector_type}</li>
            <li><strong>Rating:</strong> ${point.Rating} / 5</li>
        </ul>
        <div class="mt-auto d-flex justify-content-center">
            <a href="BookChargePoints.php?id=${point.Charger_point_ID}" 
               class="add-btn w-75 py-2 text-center">Book now</a>
        </div>
    </div>
`;
}

// The below code is for List View
// Function to render the charger points in the list view
function loadChargerList() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/SearchChargePoints.php?action=getChargers', true);
    xhr.responseType = 'json';

    xhr.onload = function () {
        if (xhr.status === 200) {
            const chargers = xhr.response;
            let html = '';

            chargers.forEach(function (charger) {
                html += `
<div class="col-md-4 mb-4">
    <div class="card-list shadow h-100 card-hover" data-id="${charger.Charger_point_ID}">
    <!-- Div for the charger point image -->
        <div class="card-img-top bg-light d-flex justify-content-center align-items-center" style="height: 180px;">
            <img src="/images/ChargerPoints/${charger.Charger_image_url}" alt="${charger.Name}" class="img-fluid" style="max-height: 100%; max-width: 100%;">
        </div>

        <div class="card-body d-flex flex-column justify-content-between">
            <h5 class="card-title fw-bold">${charger.Name}</h5>
            <p class="card-text">${charger.Charger_point_description}</p>
            <ul class="list-unstyled small">
                <li><strong>Price:</strong> $${charger.Price_per_kWatt}/kWh</li>
                <li><strong>Connector:</strong> ${charger.Connector_type}</li>
                <li><strong>Status:</strong> ${charger.Availability_status}</li>
                <li><strong>Rating:</strong> ${charger.Rating}</li>
            </ul>
            <div class="d-flex justify-content-center">
                <a href="BookChargePoints.php?id=${charger.Charger_point_ID}" class="add-btn w-50 py-1 d-flex justify-content-center align-items-center mb-3">Book Now</a>
            </div>
        </div>
    </div>
</div>`;
            });

            document.getElementById('charger-list').innerHTML = html;
        } else {
            document.getElementById('charger-list').innerHTML = '<div class="col-12 text-danger">Failed to load charger points.</div>';
        }
    };

    xhr.onerror = function () {
        document.getElementById('charger-list').innerHTML = '<div class="col-12 text-danger">Failed to load charger points.</div>';
    };

    xhr.send();
}


// Code below is for filter system
// Function to calculate the distance
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth radius in km
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function toRad(deg) {
    return deg * Math.PI / 180;
}
$(document).ready(function () {
    // Show or hide filter panel
    $("#filterButton").on("click", function () {
        $("#filter-panel").toggleClass("d-none");
    });

    // Update displayed price when range slider moves
    $("#priceRange").on("input", function () {
        $("#priceValue").text($(this).val());
    });

    // Apply filters
    $("#applyFilters").on("click", function () {
        const nearest = $("#nearest").is(":checked");
        const maxPrice = parseFloat($("#priceRange").val());
        const availability = $("#availability").val();

        const filtered = allChargers.filter(point => {
            const lat = parseFloat(point.Latitude);
            const lng = parseFloat(point.Longitude);
            const price = parseFloat(point.Price_per_kWatt);
            const availabilityStatus = point.Availability_status;

            if (isNaN(lat) || isNaN(lng)) return false;
            if (!isNaN(price) && price > maxPrice) return false;
            if (availability && availability !== "" && availabilityStatus !== availability) return false;

            if (nearest && userPosition) {
                const distance = getDistance(userPosition.lat, userPosition.lng, lat, lng);
                if (distance > 10) return false;
            }

            return true;
        });

        // Update map markers
        displayChargers(filtered);

        // Hide/show list cards based on filter
        $(".card-list").each(function () {
            const id = $(this).data("id");
            const match = filtered.some(ch => ch.Charger_point_ID === id);
            $(this).closest(".col-md-4").toggle(match);
        });

        // Hide filter panel
        $("#filter-panel").addClass("d-none");
    });

    // Reset all filters
    $("#resetButton").on("click", function () {
        $("#nearest").prop("checked", false);
        $("#priceRange").val(10);
        $("#priceValue").text(10);
        $("#availability").val("");

        // Reset map
        displayChargers(allChargers);

        // Show all list cards
        $(".card-list").closest(".col-md-4").show();

        // Hide filter panel
        $("#filter-panel").addClass("d-none");
    });
});


