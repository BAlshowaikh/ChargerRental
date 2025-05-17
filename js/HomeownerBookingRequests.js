let currentBookingId = null;

function openStatusModal(id, startTime, statusId) {
    currentBookingId = id;

    const overlay = document.getElementById("statusModalOverlay");
    const statusText = document.getElementById("statusModalText");
    const buttons = document.getElementById("statusModalButtons");
    const lockedMsg = document.getElementById("statusLockedMessage");

    // Reset
    overlay.classList.remove("hidden");
    overlay.style.display = "flex";

    if (statusId === 1) {
        // PENDING (editable)
        statusText.innerHTML = `<span class="text-dark">📅 This booking starts on <strong class="text-primary">${startTime}</strong>.</span><br><span class="text-success">Would you like to approve or decline it?</span>`;
        buttons.classList.remove("d-none");
        lockedMsg.classList.add("d-none");
    } else {
        // FINALIZED (approved or declined)
        statusText.innerHTML = `<span class="text-muted">📅 This booking starts on <strong class="text-secondary">${startTime}</strong>.</span>`;
        buttons.classList.add("d-none");
        lockedMsg.classList.remove("d-none");
    }
}

function submitStatusAjax(status) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "UpdateBookingStatus.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                // Close the modal first
                const overlay = document.getElementById("statusModalOverlay");
                overlay.classList.add("hidden");
                setTimeout(() => overlay.style.display = "none", 300);

                // Slight delay before showing message (makes it feel smoother)
                setTimeout(() => {
                    if (response.success) {
                        const message = status === 2
                            ? "✅ Booking approved! You're all set for that time slot. 🔌"
                            : "❌ Booking declined. That slot is now free again.";

                        showFlash(message, "success");
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showFlash("⚠️ " + response.message, "error");
                    }
                }, 300);
            } catch (e) {
                console.error("Response parse error:", e);
            }
        }
    };

    xhr.send("booking_id=" + encodeURIComponent(currentBookingId) + "&status=" + encodeURIComponent(status));
}

document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.createElement("div");
    overlay.id = "statusModalOverlay";
    overlay.className = "status-modal-overlay";
    overlay.style.display = "none";

    overlay.innerHTML = `
    <div class="status-modal text-center animate__animated animate__fadeInDown">
        <button class="status-modal-close" id="statusModalClose">&times;</button>
        <h4 class="mb-3 fw-bold text-primary">📝 Confirm Action</h4>
        <p class="text-muted mb-2" id="statusModalText"></p>
        <div class="d-flex justify-content-center gap-3 my-3" id="statusModalButtons">
            <button type="button" class="btn btn-success px-4 py-2 fw-bold shadow" onclick="submitStatusAjax(2)">
                ✅ Yes! Approve it
            </button>
            <button type="button" class="btn btn-danger px-4 py-2 fw-bold shadow" onclick="submitStatusAjax(3)">
                ❌ Nope, decline this
            </button>
        </div>
        <div id="statusLockedMessage" class="text-danger small d-none">
            🚫 <strong>This booking is finalized!</strong><br>Sorry, you can no longer make changes to it.
        </div>
    </div>
    `;

    document.body.appendChild(overlay);

    document.getElementById("statusModalClose").addEventListener("click", () => {
        document.getElementById("statusModalOverlay").style.display = "none";
    });
});

function showFlash(message, type) {
    const flash = document.createElement('div');
    flash.className = `alert alert-${type === 'success' ? 'success' : 'danger'} flash-message`;
    flash.textContent = message;

    flash.style.position = 'fixed';
    flash.style.top = '20px';
    flash.style.left = '50%';
    flash.style.transform = 'translateX(-50%)';
    flash.style.zIndex = '1050';
    flash.style.padding = '1rem 2rem';
    flash.style.borderRadius = '30px';
    flash.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    flash.style.fontWeight = 'bold';

    document.body.appendChild(flash);

    setTimeout(() => {
        flash.remove();
    }, 3000);
}
