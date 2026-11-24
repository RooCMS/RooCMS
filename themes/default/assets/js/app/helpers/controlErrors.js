// Accessible UI Kit field errors. Shared by settings and the live examples.
let control_error_id = 0;

export function clear_control_error(field) {
    const error_id = field.dataset.controlErrorId;
    if (!error_id) return;
    field.ownerDocument.getElementById(error_id)?.remove();
    const descriptions = (field.getAttribute('aria-describedby') || '').split(/\s+/)
        .filter((id) => id && id !== error_id);
    if (descriptions.length) field.setAttribute('aria-describedby', descriptions.join(' '));
    else field.removeAttribute('aria-describedby');
    field.removeAttribute('aria-invalid');
    delete field.dataset.controlErrorId;
}

export function set_control_error(field, message) {
    clear_control_error(field);
    const error = field.ownerDocument.createElement('span');
    error.id = `control-error-${++control_error_id}`;
    error.className = 'ui-error block';
    error.textContent = message;
    error.setAttribute('role', 'alert');
    field.dataset.controlErrorId = error.id;
    field.setAttribute('aria-invalid', 'true');
    const descriptions = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
    field.setAttribute('aria-describedby', [...descriptions, error.id].join(' '));
    const container = field.closest('.field-container') || field.parentElement;
    container.appendChild(error);
}

export function clear_control_errors(root = document) {
    root.querySelectorAll('[data-control-error-id]').forEach(clear_control_error);
}
