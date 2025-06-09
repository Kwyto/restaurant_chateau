document.addEventListener('DOMContentLoaded', function() {
    // Login form validation
    const loginForm = document.querySelector('form[action="authenticate.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = this.querySelector('#email');
            const password = this.querySelector('#password');
            
            if (!email.value || !password.value) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    }

    // Signup form validation
    const signupForm = document.querySelector('form[action="register.php"]');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const firstName = this.querySelector('#first_name');
            const lastName = this.querySelector('#last_name');
            const email = this.querySelector('#email');
            const password = this.querySelector('#password');
            const confirmPassword = this.querySelector('#confirm_password');
            const terms = this.querySelector('#terms');
            
            let isValid = true;
            
            if (!firstName.value || !lastName.value || !email.value || !password.value || !confirmPassword.value) {
                isValid = false;
                alert('Please fill in all required fields');
            }
            
            if (password.value !== confirmPassword.value) {
                isValid = false;
                alert('Passwords do not match');
            }
            
            if (!terms.checked) {
                isValid = false;
                alert('You must agree to the terms and conditions');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Booking form validation
    const bookingForm = document.getElementById('book-now');
    if (bookingForm) {
        bookingForm.addEventListener('click', function(e) {
            const guests = document.getElementById('guests');
            const date = document.getElementById('date');
            const time = document.getElementById('time');
            
            if (!guests.value || !date.value || !time.value) {
                e.preventDefault();
                alert('Please fill in all required reservation details');
            }
        });
    }

    // Payment form validation
    const paymentForm = document.getElementById('complete-reservation');
    if (paymentForm) {
        paymentForm.addEventListener('click', function(e) {
            const terms = document.getElementById('terms');
            const paymentMethod = document.querySelector('input[name="payment-method"]:checked');
            
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
                return;
            }
            
            if (paymentMethod.value === 'credit-card') {
                const cardNumber = document.getElementById('card-number');
                const cardName = document.getElementById('card-name');
                const cardExpiry = document.getElementById('card-expiry');
                const cardCvv = document.getElementById('card-cvv');
                
                if (!cardNumber.value || !cardName.value || !cardExpiry.value || !cardCvv.value) {
                    e.preventDefault();
                    alert('Please fill in all credit card details');
                    return;
                }
            }
            
            if (!terms.checked) {
                e.preventDefault();
                alert('You must agree to the terms and conditions');
            }
        });
    }
});