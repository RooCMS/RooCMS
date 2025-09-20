// CSP-compatible Alpine.js registration form logic
// Define data immediately in global scope for Alpine CSP compatibility

window.registerFormData = {
    form: {
        login: '',
        email: '',
        password: '',
        password_confirmation: ''
    },
    errors: {},
    success: '',
    loading: false
};

// Clear error for specific field
window.clearRegisterError = function(field) {
    if (window.registerFormData.errors[field]) {
        delete window.registerFormData.errors[field];
    }
    window.registerFormData.errors.general = '';
};

// Validate registration form
window.validateRegisterForm = function() {
    window.registerFormData.errors = {};

    // Валидация логина
    if (!window.registerFormData.form.login.trim()) {
        window.registerFormData.errors.login = 'Логин обязателен для заполнения';
    } else if (window.registerFormData.form.login.length < 5) {
        window.registerFormData.errors.login = 'Логин должен содержать минимум 5 символов';
    } else if (window.registerFormData.form.login.length > 30) {
        window.registerFormData.errors.login = 'Логин не должен превышать 30 символов';
    }

    // Валидация email
    if (!window.registerFormData.form.email.trim()) {
        window.registerFormData.errors.email = 'Email обязателен для заполнения';
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(window.registerFormData.form.email)) {
            window.registerFormData.errors.email = 'Введите корректный email адрес';
        }
    }

    // Валидация пароля
    if (!window.registerFormData.form.password) {
        window.registerFormData.errors.password = 'Пароль обязателен для заполнения';
    } else if (window.registerFormData.form.password.length < 8) {
        window.registerFormData.errors.password = 'Пароль должен содержать минимум 8 символов';
    }

    // Валидация подтверждения пароля
    if (!window.registerFormData.form.password_confirmation) {
        window.registerFormData.errors.password_confirmation = 'Подтверждение пароля обязательно';
    } else if (window.registerFormData.form.password !== window.registerFormData.form.password_confirmation) {
        window.registerFormData.errors.password_confirmation = 'Пароли не совпадают';
    }

    return Object.keys(window.registerFormData.errors).length === 0;
};

// Submit registration form
window.submitRegisterForm = async function() {
    if (!window.validateRegisterForm()) {
        return;
    }

    window.registerFormData.loading = true;
    window.registerFormData.success = '';
    window.registerFormData.errors.general = '';

    try {
        const response = await fetch('/api/v1/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                login: window.registerFormData.form.login.trim(),
                email: window.registerFormData.form.email.trim(),
                password: window.registerFormData.form.password,
                password_confirmation: window.registerFormData.form.password_confirmation
            })
        });

        const data = await response.json();

        if (response.ok) {
            window.registerFormData.success = 'Регистрация прошла успешно! Перенаправляем на страницу входа...';
            window.registerFormData.form = { login: '', email: '', password: '', password_confirmation: '' };

            // Перенаправление через 3 секунды
            setTimeout(() => {
                window.location.href = '/login';
            }, 3000);
        } else {
            if (data.details && data.details.validation_errors) {
                // Обработка ошибок валидации от сервера
                Object.entries(data.details.validation_errors).forEach(([field, message]) => {
                    window.registerFormData.errors[field] = message;
                });
            } else {
                window.registerFormData.errors.general = data.message || 'Произошла ошибка при регистрации';
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        window.registerFormData.errors.general = 'Произошла ошибка сети. Попробуйте еще раз.';
    } finally {
        window.registerFormData.loading = false;
    }
};

console.log('Registration module loaded');
