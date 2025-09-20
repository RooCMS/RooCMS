import { login } from './../app/auth.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
        login: '',
        password: '',
        login_error: '',
        login_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;
            
            this.loading = true;
            this.login_error = '';
            this.login_success = '';
            
            try {
                const result = await login(this.login, this.password);
                this.login_success = 'Вход выполнен успешно! Перенаправление...';
                
                // Перенаправление после успешного входа
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
                
            } catch (error) {
                this.login_error = error.message || 'Ошибка при входе в систему';
            } finally {
                this.loading = false;
            }
        }
    }));
});
