// Enhance native selects without replacing form values, validation or Alpine x-model.
export function init_custom_selects(root) {
    const controls = new Map();
    let sequence = 0;

    const enhance = (select) => {
        if (controls.has(select) || select.multiple || select.size > 1) return;
        const button = document.createElement('button');
        const menu = document.createElement('div');
        const original_tabindex = select.getAttribute('tabindex');
        const original_aria_hidden = select.getAttribute('aria-hidden');
        const form = select.form;
        const id = `acp-select-${++sequence}`;
        button.type = 'button';
        button.setAttribute('role', 'combobox');
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-controls', id);
        button.setAttribute('aria-expanded', 'false');
        menu.id = id;
        menu.className = 'ui-select-menu';
        menu.setAttribute('role', 'listbox');
        menu.setAttribute('popover', 'auto');
        select.classList.add('ui-select-native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        select.after(button);
        document.body.append(menu);
        let active = -1;
        let search = '';
        let search_time = 0;
        let destroyed = false;

        const is_open = () => menu.matches(':popover-open');
        const options = () => [...select.options];
        const enabled = (option) => Boolean(option?.parentElement) && !option.disabled && !option.hidden
            && !(option.parentElement.tagName === 'OPTGROUP' && option.parentElement.disabled);
        const close = () => { if (is_open()) menu.hidePopover(); };
        const highlight = (index) => {
            active = index;
            menu.querySelectorAll('[role="option"]').forEach((item) => {
                item.classList.toggle('is-active', Number(item.dataset.index) === active);
            });
            const item = menu.querySelector(`[data-index="${active}"]`);
            if (item) {
                button.setAttribute('aria-activedescendant', item.id);
                item.scrollIntoView({ block: 'nearest' });
            } else button.removeAttribute('aria-activedescendant');
        };
        const render = () => {
            menu.replaceChildren();
            let group = null;
            options().forEach((option, index) => {
                if (option.hidden) return;
                const parent = option.parentElement;
                if (parent.tagName === 'OPTGROUP' && parent !== group) {
                    const heading = document.createElement('div');
                    heading.className = 'ui-select-group';
                    heading.textContent = parent.label;
                    menu.append(heading);
                }
                group = parent;
                const item = document.createElement('div');
                item.id = `${id}-${index}`;
                item.dataset.index = String(index);
                item.className = 'ui-select-option';
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', String(option.selected));
                item.setAttribute('aria-disabled', String(!enabled(option)));
                item.textContent = option.label;
                menu.append(item);
            });
        };
        const sync = () => {
            if (destroyed) return;
            // x-for can create options after x-model has applied its initial value.
            const model_value = select._x_model?.get();
            if (model_value !== undefined && options().some((option) => option.value === String(model_value ?? ''))) {
                select.value = String(model_value ?? '');
            }
            button.className = select.className.replace(/\bui-select-native\b/g, '') + ' ui-select-trigger';
            button.textContent = select.selectedOptions[0]?.label ?? '—';
            button.disabled = select.matches(':disabled');
            button.tabIndex = original_tabindex === null ? 0 : Number(original_tabindex);
            for (const name of ['aria-invalid', 'aria-describedby', 'aria-required']) {
                const value = select.getAttribute(name);
                if (value !== null) button.setAttribute(name, value);
                else button.removeAttribute(name);
            }
            if (select.required) button.setAttribute('aria-required', 'true');
            const label_text = [...select.labels].map((label) => {
                const copy = label.cloneNode(true);
                copy.querySelectorAll('select, button').forEach((control) => control.remove());
                return copy.textContent.trim();
            }).join(' ');
            const labelled_by = select.getAttribute('aria-labelledby');
            if (labelled_by) button.setAttribute('aria-labelledby', labelled_by);
            else button.setAttribute('aria-label', select.getAttribute('aria-label') || label_text || button.textContent);
            menu.setAttribute('aria-label', label_text || select.getAttribute('aria-label') || 'Выберите значение');
            if (button.disabled || !select.getClientRects().length) close();
            if (is_open()) { render(); highlight(select.selectedIndex); }
        };
        const open = () => {
            if (button.disabled || is_open()) return;
            render();
            const rect = button.getBoundingClientRect();
            const below = window.innerHeight - rect.bottom - 12;
            const above = rect.top - 12;
            const upwards = below < 180 && above > below;
            menu.style.width = `${Math.min(rect.width, window.innerWidth - 16)}px`;
            menu.style.left = `${Math.max(8, Math.min(rect.left, window.innerWidth - rect.width - 8))}px`;
            menu.style.maxHeight = `${Math.max(48, Math.min(280, upwards ? above : below))}px`;
            menu.style.top = upwards ? 'auto' : `${rect.bottom + 4}px`;
            menu.style.bottom = upwards ? `${window.innerHeight - rect.top + 4}px` : 'auto';
            menu.showPopover();
            button.setAttribute('aria-expanded', 'true');
            highlight(enabled(select.options[select.selectedIndex]) ? select.selectedIndex
                : options().findIndex(enabled));
        };
        const choose = (index) => {
            if (!select.options[index] || !enabled(select.options[index])) return;
            select.selectedIndex = index;
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
            close();
            sync();
            button.focus();
        };
        button.addEventListener('click', (event) => {
            event.preventDefault();
            if (is_open()) close(); else open();
        });
        button.addEventListener('keydown', (event) => {
            const keys = ['ArrowDown', 'ArrowUp', 'Home', 'End', 'Enter', ' ', 'Escape'];
            if (event.key === 'Tab') { close(); return; }
            if (event.key === 'Escape') { if (is_open()) { event.preventDefault(); close(); } return; }
            if (keys.includes(event.key)) {
                event.preventDefault();
                if (event.key === 'Enter' || event.key === ' ') {
                    if (is_open()) choose(active); else open();
                    return;
                }
                open();
                const indices = options().map((option, index) => enabled(option) ? index : -1).filter((index) => index >= 0);
                const position = indices.indexOf(active);
                const next = event.key === 'Home' ? indices[0] : event.key === 'End' ? indices.at(-1)
                    : indices[Math.max(0, Math.min(indices.length - 1, position + (event.key === 'ArrowDown' ? 1 : -1)))];
                if (next !== undefined) highlight(next);
            } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                event.preventDefault();
                search = Date.now() - search_time > 700 ? event.key : search + event.key;
                search_time = Date.now();
                open();
                const index = options().findIndex((option) => enabled(option) && option.label.toLocaleLowerCase().startsWith(search.toLocaleLowerCase()));
                if (index >= 0) highlight(index);
            }
        });
        menu.addEventListener('pointerdown', (event) => event.preventDefault());
        menu.addEventListener('click', (event) => {
            const item = event.target.closest('[role="option"]');
            if (item) choose(Number(item.dataset.index));
        });
        menu.addEventListener('toggle', () => {
            button.setAttribute('aria-expanded', String(is_open()));
            if (!is_open()) button.removeAttribute('aria-activedescendant');
        });
        const focus = () => button.focus();
        const invalid = (event) => { event.preventDefault(); button.focus(); };
        const reset = () => window.Alpine ? window.Alpine.nextTick(sync) : setTimeout(sync, 0);
        select.addEventListener('focus', focus);
        select.addEventListener('invalid', invalid);
        select.addEventListener('change', sync);
        form?.addEventListener('reset', reset);
        const observer = new MutationObserver(sync);
        observer.observe(select, { subtree: true, childList: true, characterData: true, attributes: true,
            attributeFilter: ['disabled', 'selected', 'label', 'value', 'hidden', 'class', 'aria-invalid', 'aria-describedby', 'required'] });
        // Alpine's native x-model remains the source of truth, including asynchronous API updates.
        let effect;
        window.Alpine?.nextTick(() => {
            if (destroyed) return;
            effect = window.Alpine.effect(() => {
                select._x_model?.get();
                queueMicrotask(sync);
            });
        });
        sync();
        controls.set(select, { close, menu, destroy() {
            destroyed = true;
            close(); observer.disconnect();
            if (effect) window.Alpine.release(effect);
            form?.removeEventListener('reset', reset);
            select.removeEventListener('focus', focus);
            select.removeEventListener('invalid', invalid);
            select.removeEventListener('change', sync);
            select.classList.remove('ui-select-native');
            if (original_tabindex === null) select.removeAttribute('tabindex');
            else select.setAttribute('tabindex', original_tabindex);
            if (original_aria_hidden === null) select.removeAttribute('aria-hidden');
            else select.setAttribute('aria-hidden', original_aria_hidden);
            button.remove(); menu.remove();
        } });
    };
    const scan = () => {
        for (const [select, control] of controls) {
            if (!root.contains(select)) { control.destroy(); controls.delete(select); }
        }
        root.querySelectorAll('select').forEach(enhance);
    };
    scan();
    new MutationObserver(scan).observe(root, { childList: true, subtree: true });
    window.addEventListener('resize', () => controls.forEach((control) => control.close()));
    document.addEventListener('scroll', (event) => {
        controls.forEach((control) => { if (!control.menu.contains(event.target)) control.close(); });
    }, true);
}
