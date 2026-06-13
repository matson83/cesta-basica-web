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

document.querySelectorAll('dialog[data-form-dialog]').forEach((dialog) => {
    dialog.querySelector('form')?.addEventListener('submit', (event) => {
        event.preventDefault();
        alert('Funcionalidade disponível após integração com o backend.');
        dialog.close();
    });
});
