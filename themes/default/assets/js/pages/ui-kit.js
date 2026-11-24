// UI-KIT Modal Examples - для CSP совместимости
window.modalExamples = {
    // Multi-step login form
    async loginFlow() {
        const email = await window.Alpine.store('modal').show('Вход в систему', 'Введите ваш email для входа в личный кабинет.', 'Продолжить', 'Отмена', 'info');
        if (email) {
            const password = await window.Alpine.store('modal').show('Пароль', 'Введите пароль для завершения входа.', 'Войти', 'Назад', 'info');
            if (password) {
                await window.Alpine.store('modal').show('Успешно!', 'Добро пожаловать в RooCMS! Вы успешно вошли в систему.', 'OK', '', 'success');
            }
        }
    },

    // Confirmation of deletion with double check
    async deleteFlow() {
        const firstConfirm = await window.Alpine.store('modal').show('Удалить элемент?', 'Вы действительно хотите удалить этот элемент? Это действие нельзя отменить.', 'Удалить', 'Отмена', 'danger');
        if (firstConfirm) {
            const secondConfirm = await window.Alpine.store('modal').show('Подтвердите удаление', 'Пожалуйста, подтвердите удаление ещё раз. Данные будут потеряны навсегда.', 'Удалить навсегда', 'Отмена', 'danger');
            if (secondConfirm) {
                await window.Alpine.store('modal').show('Удалено!', 'Элемент успешно удалён из системы.', 'OK', '', 'success');
            }
        }
    },


    /**
     * Show warning modal window
     * @returns {Promise<boolean>}
     */
    async showWarning() {
        return await window.Alpine.store('modal').show('Подтвердите действие', 'Вы действительно хотите выполнить это действие?', 'Да', 'Отмена', 'warning');
    },

    /**
     * Show feedback modal window
     * @returns {Promise<boolean>}
     */
    async showFeedback() {
        return await window.Alpine.store('modal').show('Обратная связь', 'Расскажите нам о своём опыте использования RooCMS. Ваши комментарии помогут нам стать лучше!', 'Отправить', 'Отмена', 'info');
    },

    /**
     * Show image modal window
     * @returns {Promise<boolean>}
     */
    async showImage() {
        return await window.Alpine.store('modal').show('Изображение', 'Здесь может отображаться изображение, галерея или другой медиа-контент. В реальном приложении здесь может быть &lt;img&gt; тег или iframe с видео.', 'Понятно', '', 'info');
    }
};
