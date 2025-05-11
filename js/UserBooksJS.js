function loadChargerDetails(chargerId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "getChargerDetails.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const charger = JSON.parse(xhr.responseText);

            const popup = document.createElement('div');
            popup.className = 'popup-overlay';
            popup.id = 'popup';

            popup.innerHTML = `
                <div class="popup-card">
                    <span class="popup-close" onclick="document.getElementById('popup').remove()">&times;</span>
                    <h5 class="mb-3">Charger Point Details</h5>
                    <p><strong>Name:</strong> ${charger.name}</p>
                    <p><strong>Description:</strong> ${charger.description.replace(/\n/g, '<br>')}</p>
                    <p><strong>Connector Type:</strong> ${charger.connector_type}</p>
                    <p><strong>Price per kWh:</strong> ${charger.price_per_kw} BD</p>
                    <p><strong>Rating:</strong> ${charger.rating} / 5</p>
                </div>
            `;

            document.body.appendChild(popup);
        }
    };

    xhr.send("charger_id=" + encodeURIComponent(chargerId));
}
