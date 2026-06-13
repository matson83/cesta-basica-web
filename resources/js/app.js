document.querySelectorAll('[data-dialog-open]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const dialog = document.getElementById(trigger.dataset.dialogOpen);

        if (dialog instanceof HTMLDialogElement) {
            dialog.showModal();
        }
    });
});

document.querySelectorAll('[data-dialog-close]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
        trigger.closest('dialog')?.close();
    });
});

// Reabre modais após erro de validação (redirect back do Laravel).
document.querySelectorAll('dialog[data-form-dialog]').forEach((dialog) => {
    if (dialog.dataset.reopen === 'true' && dialog instanceof HTMLDialogElement) {
        dialog.showModal();
    }
});
