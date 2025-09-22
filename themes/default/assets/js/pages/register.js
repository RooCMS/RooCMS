
document.addEventListener('alpine:init', () => {
    Alpine.data('registerForm', () => ({
        login: '',
        email: '',
        password: '',
        password_confirmation: '',
        form_error: '',
        form_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;
            
            this.loading = true;
            this.form_error = '';
            this.form_success = '';
            
        }
    }));
});