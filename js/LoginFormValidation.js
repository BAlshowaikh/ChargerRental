import JustValidate from "./just-validate.es.js";

document.addEventListener('DOMContentLoaded', function () {
    const validation = new JustValidate('#loginForm', {
        validateBeforeSubmitting: true,
        validateOnInput: true
    });

    validation
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
        .onSuccess((event) => {
            //event.preventDefault();
            event.target.submit(); // only submit if validation passed
        });
});
