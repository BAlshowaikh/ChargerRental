if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;
        },
        function(error) {
            console.error("Geolocation error: " + error.message);
            alert("Failed to get your location. Please allow location access.");
        }
    );
} else {
    alert("Geolocation is not supported by your browser.");
}
