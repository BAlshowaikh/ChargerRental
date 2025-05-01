import JustValidate from "./just-validate.es.js";

document.addEventListener('DOMContentLoaded', function () {
    const validation = new JustValidate('#regForm', {
        validateBeforeSubmitting: true,
        validateOnInput: true
    });

    validation
        .addField('#fname', [
            {
                rule: 'required',
                errorMessage: 'First name is required',
            },
            {
                rule: 'minLength',
                value: 3,
                errorMessage: 'First name must be at least 3 characters',
            },
        ])
        .addField('#lname', [
            {
                rule: 'required',
                errorMessage: 'Last name is required',
            },
            {
                rule: 'minLength',
                value: 3,
                errorMessage: 'Last name must be at least 3 characters',
            },
        ])
        .addField('#email', [
            {
                rule: 'required',
                errorMessage: 'Email is required',
            },
            {
                rule: 'email',
                errorMessage: 'Email is not valid',
            },
        ])
        .addField('#password', [
            {
                rule: 'required',
                errorMessage: 'Password is required',
            },
            {
                rule: 'minLength',
                value: 6,
                errorMessage: 'Password must be at least 6 characters',
            },
        ])
        .addField('#phno', [
            {
                rule: 'required',
                errorMessage: 'Phone number is required',
            },
            {
                validator: (value) => /^[0-9]{8,}$/.test(value),
                errorMessage: 'Phone number must be at least 8 digits',
            },
        ])
        /*.addField('#address', [
            {
                rule: 'required',
                errorMessage: 'Address is required',
            },
            {
                validator: (value) => /^[a-zA-Z0-9\s,\-]+$/.test(value),
                errorMessage: 'Address format is invalid',
            },
        ])*/
        .onSuccess((event) => {
            //event.preventDefault();
            event.target.submit(); // only submit if validation passed
        });
});
