import JustValidate from "./just-validate.es.js";

document.addEventListener("DOMContentLoaded", function()  {
    const validation = new JustValidate("#homeownerRegForm", {
        validateBeforeSubmitting: true,
        validateOnInput: true,
    });

    validation
        // First Name
        .addField("#fname", [
            {
                rule: "required",
                errorMessage: "First name is required",
            },
            {
                rule: "minLength",
                value: 3,
                errorMessage: "First name must be at least 3 characters",
            },
        ])

        // Last Name
        .addField("#lname", [
            {
                rule: "required",
                errorMessage: "Last name is required",
            },
            {
                rule: "minLength",
                value: 3,
                errorMessage: "Last name must be at least 3 characters",
            },
        ])

        // Email
        .addField("#email", [
            {
                rule: "required",
                errorMessage: "Email is required",
            },
            {
                rule: "email",
                errorMessage: "Email is not valid",
            },
        ])

        // Password
        .addField("#password", [
            {
                rule: "required",
                errorMessage: "Password is required",
            },
            {
                rule: "minLength",
                value: 6,
                errorMessage: "Password must be at least 6 characters",
            },
        ])

        // Phone Number
        .addField("#phno", [
            {
                rule: "required",
                errorMessage: "Phone number is required",
            },
            {
                validator: (value) => /^[0-9]{8,}$/.test(value),
                errorMessage: "Phone number must be at least 8 digits",
            },
        ])

        // Charge Point Description
        .addField("#chargePoint", [
            {
                rule: "required",
                errorMessage: "Charge point description is required",
            },
            {
                rule: "minLength",
                value: 5,
                errorMessage: "Description must be at least 5 characters",
            },
        ])

        // Price per kWh
        .addField("#price", [
            {
                rule: "required",
                errorMessage: "Price is required",
            },
            {
                validator: (value) => !isNaN(value) && parseFloat(value) > 0,
                errorMessage: "Price must be a positive number",
            },
        ])

        // Availability select
        .addField("#availability", [
            {
                rule: "required",
                errorMessage: "Please select availability",
            },
        ])
        // connector type
        .addField("#connectorType", [
            {
                rule: "required",
                errorMessage: "Connector type is required",
            },
        ])

        // File upload
        .addField("#chargePointImage", [
            {
                rule: "required",
                errorMessage: "Please select an image to upload",
            },
            {
                // direct access to the File object
                validator: (value, fields) => {
                    const fileInput = document.querySelector('#chargePointImage');
                    if (!fileInput || !fileInput.files.length) return false;
                    return fileInput.files[0].size <= 2 * 1024 * 1024; // 2MB
                },
                errorMessage: 'Image must be 2MB or smaller'
            }
        ])

        // On success, submit the form
        .onSuccess((event) => {
            event.target.submit();
        });
});
