// Toggle the visibility of the filter panel when the filter button is clicked
document.getElementById("filterButton").addEventListener("click", function() {
    var filterPanel = document.getElementById("filter-panel");

    // Toggle the d-none class to show/hide the filter panel
    if (filterPanel.classList.contains("d-none")) {
        filterPanel.classList.remove("d-none");
    } else {
        filterPanel.classList.add("d-none");
    }
});

// Update the price range value display when the slider is changed
document.getElementById("priceRange").addEventListener("input", function() {
    document.getElementById("priceValue").textContent = this.value;
});

// Apply filters (this is a placeholder for your filter logic)
document.getElementById("applyFilters").addEventListener("click", function() {
    // Get selected filter values
    var nearest = document.getElementById("nearest").checked;
    var price = document.getElementById("priceRange").value;
    var availability = document.getElementById("availability").value;

    console.log("Filters Applied: ", {
        nearest: nearest,
        price: price,
        availability: availability
    });

    // Hide the filter panel after applying the filters
    document.getElementById("filter-panel").classList.add("d-none");
});

// Get all radio buttons for the toggle group
const radios = document.querySelectorAll('.btn-check[name="viewToggle"]');
// Only get labels linked to those radios
const labels = Array.from(radios).map(radio => document.querySelector(`label[for="${radio.id}"]`));

// Apply the gradient to the label of the selected radio button
radios.forEach((radio, index) => {
    radio.addEventListener('change', () => {
        // Remove the gradient from all labels
        labels.forEach(label => label.style.background = '');

        // Apply the gradient to the clicked button's label
        if (radio.checked) {
            labels[index].style.background = 'linear-gradient(to right, #3ce4d3, #2b44d4)';
        }
    });
});

