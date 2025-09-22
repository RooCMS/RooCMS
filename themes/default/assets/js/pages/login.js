import { login } from './../app/auth.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
        login: '',
        password: '',
        form_error: '',
        form_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;
            
            this.loading = true;
            this.form_error = '';
            this.form_success = '';
            
            try {
                const result = await login(this.login, this.password);
                this.form_success = 'Login successful! Redirecting...';
                
                // Redirect after successful login
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
                
            } catch (error) {
                this.form_error = error.message || 'Error logging in';
            } finally {
                this.loading = false;
            }
        }
    }));
});
