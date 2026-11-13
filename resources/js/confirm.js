/* ==========================================================================
   TGD — Reusable dialog + toast component (vanilla JS, no dependencies)
   --------------------------------------------------------------------------
   Exposes:
     window.TGD.confirm({ title, subject, message, confirmLabel, cancelLabel,
                          danger }) -> Promise<boolean>
     window.TGD.menu({ title, subject, options: [{ key, label, hint, icon,
                          danger }] }) -> Promise<string|null>
     window.TGD.info({ title, subtitle, fields: [{ label, value, wide }] })
     window.TGD.toast(message, type)          // type: 'success' | 'error'
     window.TGD.csrf                          // current CSRF token

   Used for every destructive / status-changing action so we never fall back
   to the browser's native alert()/confirm() dialogs.
   ========================================================================== */
(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let openState = null;

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = text;
        return node;
    }

    function onKeydown(event) {
        if (!openState) return;

        if (event.key === 'Escape' && openState.dismissible) {
            event.preventDefault();
            close();
        }
    }

    function close() {
        if (!openState) return;

        const state = openState;
        openState = null;

        document.removeEventListener('keydown', onKeydown, true);
        state.root.remove();

        if (!document.querySelector('.dialog-root')) {
            document.body.classList.remove('dialog-open');
        }

        if (typeof state.onClose === 'function') state.onClose();

        if (state.restoreFocus && document.contains(state.restoreFocus)) {
            state.restoreFocus.focus();
        }
    }

    /**
     * Build and mount a dialog. Returns nothing; use onClose for teardown.
     */
    function open({ title, subtitle, tone = 'brand', variant = null, body = [], actions = [], dismissible = true, onClose }) {
        close(); // only one dialog at a time

        const titleId = 'tgd-dialog-' + Math.random().toString(36).slice(2);

        const dialog = el('div', 'dialog dialog--' + tone);

        const head = el('div', 'dialog-head');
        const heading = el('h2', 'dialog-title', title);
        heading.id = titleId;
        head.appendChild(heading);
        if (subtitle) head.appendChild(el('p', 'dialog-subtitle', subtitle));
        dialog.appendChild(head);

        const content = el('div', 'dialog-content');
        body.forEach((node) => content.appendChild(node));
        dialog.appendChild(content);

        const footer = el('div', 'dialog-actions');
        actions.forEach((node) => footer.appendChild(node));
        dialog.appendChild(footer);

        const backdrop = el('div', 'dialog-backdrop');
        const container = el('div', 'dialog-container');
        container.setAttribute('role', 'dialog');
        container.setAttribute('aria-modal', 'true');
        container.setAttribute('aria-labelledby', titleId);
        container.appendChild(dialog);

        const root = el('div', 'dialog-root' + (variant ? ' dialog-root--' + variant : ''));
        root.appendChild(backdrop);
        root.appendChild(container);
        document.body.appendChild(root);
        document.body.classList.add('dialog-open');

        openState = {
            root,
            dismissible,
            restoreFocus: document.activeElement,
            onClose,
        };

        if (dismissible) {
            backdrop.addEventListener('click', close);
        }

        document.addEventListener('keydown', onKeydown, true);
    }

    /**
     * Professional confirmation card. Resolves true only when confirmed.
     */
    function confirm(options) {
        const {
            title = 'Are you sure?',
            subject = null,
            message = '',
            confirmLabel = 'Confirm',
            cancelLabel = 'Cancel',
            danger = false,
        } = options || {};

        return new Promise((resolve) => {
            let settled = false;

            const finish = (value) => {
                if (settled) return;
                settled = true;
                close();
                resolve(value);
            };

            const cancelBtn = el('button', 'btn btn-secondary', cancelLabel);
            cancelBtn.type = 'button';
            cancelBtn.addEventListener('click', () => finish(false));

            const confirmBtn = el('button', 'btn ' + (danger ? 'btn-danger' : 'btn-primary'), confirmLabel);
            confirmBtn.type = 'button';
            confirmBtn.addEventListener('click', () => finish(true));

            const body = [];

            if (subject) {
                const subjectRow = el('div', 'dialog-subject');
                subjectRow.appendChild(el('span', 'dialog-subject-label', 'Applicant'));
                subjectRow.appendChild(el('span', 'dialog-subject-name', subject));
                body.push(subjectRow);
            }

            if (message) {
                body.push(el('p', 'dialog-message', message));
            }

            open({
                title,
                tone: danger ? 'danger' : 'brand',
                body,
                actions: [cancelBtn, confirmBtn],
                dismissible: true,
                onClose: () => finish(false),
            });

            // Destructive actions focus Cancel so Enter can't delete by accident.
            (danger ? cancelBtn : confirmBtn).focus();
        });
    }

    /**
     * Centered action card — a list of operations for one record.
     *
     * Resolves with the chosen option's `key`, or `null` when dismissed.
     * Each option may supply `icon` (trusted static HTML), `label`, `hint`
     * and `danger`. The page then runs its own confirmation + request flow.
     */
    function menu(options) {
        const {
            title = 'Manage',
            subtitle = null,
            subject = null,
            subjectLabel = 'Applicant',
            options: items = [],
            cancelLabel = 'Cancel',
        } = options || {};

        return new Promise((resolve) => {
            let settled = false;

            const finish = (value) => {
                if (settled) return;
                settled = true;
                close();
                resolve(value);
            };

            const body = [];

            if (subject) {
                const subjectRow = el('div', 'dialog-subject');
                subjectRow.appendChild(el('span', 'dialog-subject-label', subjectLabel));
                subjectRow.appendChild(el('span', 'dialog-subject-name', subject));
                body.push(subjectRow);
            }

            const list = el('div', 'dialog-menu');
            list.setAttribute('role', 'menu');

            items.forEach((item) => {
                const btn = el('button', 'dialog-menu-item' + (item.danger ? ' dialog-menu-item--danger' : ''));
                btn.type = 'button';
                btn.setAttribute('role', 'menuitem');

                if (item.icon) {
                    const icon = el('span', 'dialog-menu-icon');
                    icon.setAttribute('aria-hidden', 'true');
                    icon.innerHTML = item.icon; // trusted, app-supplied markup
                    btn.appendChild(icon);
                }

                const text = el('span', 'dialog-menu-text');
                text.appendChild(el('span', 'dialog-menu-label', item.label));
                if (item.hint) text.appendChild(el('span', 'dialog-menu-hint', item.hint));
                btn.appendChild(text);

                btn.addEventListener('click', () => finish(item.key));
                list.appendChild(btn);
            });

            body.push(list);

            const cancelBtn = el('button', 'btn btn-secondary', cancelLabel);
            cancelBtn.type = 'button';
            cancelBtn.addEventListener('click', () => finish(null));

            open({
                title,
                subtitle,
                tone: 'brand',
                variant: 'menu',
                body,
                actions: [cancelBtn],
                dismissible: true,
                onClose: () => finish(null),
            });

            cancelBtn.focus();
        });
    }

    /**
     * Read-only detail card.
     */
    function info(options) {
        const {
            title = 'Details',
            subtitle = null,
            fields = [],
            closeLabel = 'Close',
        } = options || {};

        const body = [];

        if (fields.length) {
            const list = el('dl', 'dialog-fields');

            fields.forEach((field) => {
                const row = el('div', 'dialog-field' + (field.wide ? ' dialog-field--wide' : ''));
                row.appendChild(el('dt', 'dialog-field-label', field.label));
                row.appendChild(el(
                    'dd',
                    'dialog-field-value',
                    field.value === null || field.value === undefined || field.value === '' ? '—' : String(field.value)
                ));
                list.appendChild(row);
            });

            body.push(list);
        }

        const closeBtn = el('button', 'btn btn-primary', closeLabel);
        closeBtn.type = 'button';
        closeBtn.addEventListener('click', () => close());

        open({ title, subtitle, tone: 'brand', body, actions: [closeBtn], dismissible: true });
        closeBtn.focus();
    }

    /**
     * Toast notification, auto-dismissed.
     */
    function toast(message, type = 'success') {
        let stack = document.querySelector('.toast-stack');

        if (!stack) {
            stack = el('div', 'toast-stack');
            stack.setAttribute('aria-live', 'polite');
            document.body.appendChild(stack);
        }

        const node = el('div', 'toast toast--' + type);
        node.setAttribute('role', 'status');
        node.textContent = message;
        stack.appendChild(node);

        setTimeout(() => node.classList.add('toast--out'), 3800);
        setTimeout(() => node.remove(), 4400);
    }

    window.TGD = Object.assign(window.TGD || {}, {
        confirm,
        menu,
        info,
        toast,
        csrf: CSRF,
    });
})();
