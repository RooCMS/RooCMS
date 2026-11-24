// Local UI Kit examples: no API requests and no inline handlers (CSP).
import { set_control_error, clear_control_error, clear_control_errors } from '../app/helpers/controlErrors.js';

const kit = document.getElementById('acp-ui-kit');

document.addEventListener('alpine:init', () => {
    window.Alpine.data('kitSelectDemo', () => ({
        value: 'editor',
        disabled: false,
        visible: true,
        choices: [{ value: 'admin', label: 'Администратор' }, { value: 'editor', label: 'Редактор' }],
        changeValue() { this.value = this.value === 'admin' ? 'editor' : 'admin'; },
        toggleDisabled() { this.disabled = !this.disabled; },
        toggleVisible() { this.visible = !this.visible; },
        removeOption() { this.choices = this.choices.filter((item) => item.value !== this.value); },
        addOption() {
            if (!this.choices.some((item) => item.value === 'guest')) this.choices.push({ value: 'guest', label: 'Гость' });
            this.value = 'guest';
        }
    }));
});

if (kit) {
    const form = kit.querySelector('[data-demo-form]');
    const form_status = kit.querySelector('[data-form-status]');
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        form_status.textContent = 'Форма заполнена верно. Демонстрационные данные не отправлялись.';
    });
    form.addEventListener('invalid', (event) => {
        set_control_error(event.target, event.target.validationMessage);
    }, true);
    form.addEventListener('reset', () => {
        form_status.textContent = '';
        clear_control_errors(form);
    });
    form.addEventListener('input', (event) => {
        form_status.textContent = '';
        clear_control_error(event.target);
    });

    const tabs = [...kit.querySelectorAll('[role="tab"]')];
    const activate_tab = (tab, focus = false) => {
        tabs.forEach((item) => {
            const selected = item === tab;
            item.setAttribute('aria-selected', String(selected));
            item.tabIndex = selected ? 0 : -1;
            item.className = selected ? 'btn primary' : 'btn secondary';
            document.getElementById(item.getAttribute('aria-controls')).hidden = !selected;
        });
        if (focus) tab.focus();
    };
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activate_tab(tab));
        tab.addEventListener('keydown', (event) => {
            let next;
            if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
            if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = tabs.length - 1;
            if (next === undefined) return;
            event.preventDefault();
            activate_tab(tabs[next], true);
        });
    });

    kit.querySelectorAll('[data-remove-chip]').forEach((button) => {
        button.addEventListener('click', () => {
            const chip = button.closest('[data-chip]');
            const next = chip.nextElementSibling?.querySelector('button')
                ?? chip.previousElementSibling?.querySelector('button');
            chip.remove();
            kit.querySelector('[data-chip-status]').textContent = `Метка ${button.dataset.removeChip} удалена.`;
            (next ?? kit.querySelector('[data-restore-chips]')).focus();
        });
    });
    const chips = kit.querySelector('[data-chips]');
    // Keep the original nodes so their event handlers survive restoration.
    const initial_chips = [...chips.children];
    kit.querySelector('[data-restore-chips]').addEventListener('click', () => {
        chips.replaceChildren(...initial_chips);
        kit.querySelector('[data-chip-status]').textContent = 'Все метки восстановлены.';
    });

    const pagination = kit.querySelector('[data-pagination]');
    const page_buttons = [...pagination.querySelectorAll('[data-page]')];
    const previous = pagination.querySelector('[data-previous]');
    const next = pagination.querySelector('[data-next]');
    let page = 2;
    const select_page = (value) => {
        page = Math.max(1, Math.min(10, value));
        // Show a moving window, always retaining the first and last pages.
        const middle = Math.max(3, Math.min(8, page));
        [1, middle - 1, middle, middle + 1, 10].forEach((number, index) => {
            const button = page_buttons[index];
            button.dataset.page = String(number);
            button.textContent = String(number);
            button.className = number === page ? 'btn primary small' : 'btn secondary small';
            if (number === page) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
        previous.disabled = page === 1;
        next.disabled = page === 10;
        kit.querySelector('[data-page-status]').textContent = `Страница ${page} из 10`;
    };
    page_buttons.forEach((button) => button.addEventListener('click', () => select_page(Number(button.dataset.page))));
    previous.addEventListener('click', () => select_page(page - 1));
    next.addEventListener('click', () => select_page(page + 1));
    select_page(page);

    const search = kit.querySelector('#kit-search');
    const rows = [...kit.querySelectorAll('#kit-orders-table tbody tr')];
    const filter_orders = () => {
        const query = search.value.trim().toLocaleLowerCase('ru');
        let count = 0;
        rows.forEach((row) => {
            row.hidden = !row.textContent.toLocaleLowerCase('ru').includes(query);
            if (!row.hidden) count++;
        });
        kit.querySelector('[data-search-status]').textContent = count
            ? `Показано заказов: ${count}` : 'По вашему запросу ничего не найдено.';
    };
    search.addEventListener('input', filter_orders);
    search.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            filter_orders();
            kit.querySelector('#kit-orders').scrollIntoView({ block: 'start' });
        }
    });
    kit.querySelector('[data-search]').addEventListener('click', () => {
        filter_orders();
        kit.querySelector('#kit-orders').scrollIntoView({ block: 'start' });
    });

    kit.querySelectorAll('[data-modal-type]').forEach((button) => {
        button.addEventListener('click', async () => {
            const type = button.dataset.modalType;
            const confirmed = await window.modal('Пример модального окна',
                'Это демонстрация. Действия с данными не выполняются.', 'Продолжить', 'Отмена', type);
            kit.querySelector('[data-modal-status]').textContent = confirmed ? 'Действие подтверждено.' : 'Действие отменено.';
        });
    });
}
