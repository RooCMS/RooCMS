import { login } from '../app/auth.js';

function loginForm(){
    return {
        login: '',
        password: '',
        loading: false,
        error: '',
        async onSubmit(){
            this.error = ''; this.loading = true;
            try {
                const res = await login(this.login, this.password);
                window.location.href = '/users/me';
            } catch(e) {
                this.error = (e && e.message) ? e.message : 'Ошибка входа';
            } finally {
                this.loading = false;
            }
        }
    };
}

window.loginForm = loginForm;

// Signal that page scripts are ready
window.__roocmsPageScriptsReady = true;
window.dispatchEvent(new Event('roocms:pages-ready'));


