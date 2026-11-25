export function show_notice(message, type = 'success') {
    let stack = document.getElementById('ui-notices');
    if (!stack) {
        stack = document.createElement('div');
        stack.id = 'ui-notices';
        stack.className = 'ui-notices';
        document.body.append(stack);
    }

    const notice = document.createElement('div');
    const variant = type === 'error' ? 'danger' : type;
    notice.className = `alert ui-notice ${variant}`;
    notice.setAttribute('role', variant === 'danger' ? 'alert' : 'status');

    const text = document.createElement('span');
    text.className = 'min-w-0 flex-1';
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'ui-notice-close';
    close.setAttribute('aria-label', 'Dismiss notification');
    close.textContent = '×';

    const timer = setTimeout(() => notice.remove(), 15000);
    close.addEventListener('click', () => {
        clearTimeout(timer);
        notice.remove();
    });

    notice.append(text, close);
    stack.append(notice);
}
