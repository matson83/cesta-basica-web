document.querySelectorAll('[data-dialog-open]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const dialog = document.getElementById(trigger.dataset.dialogOpen);

        if (dialog instanceof HTMLDialogElement) {
            if (dialog.open) {
                dialog.close();
            }

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

document.querySelectorAll('[data-table-search]').forEach((input) => {
    const table = document.querySelector(input.dataset.tableSearch);
    const rows = table?.querySelectorAll('tbody tr[data-search-row]');

    if (!rows?.length) {
        return;
    }

    const normalize = (value) => value
        .toString()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .toLowerCase();

    const applySearch = () => {
        const query = normalize(input.value);

        rows.forEach((row) => {
            row.hidden = query !== '' && !normalize(row.dataset.searchRow || row.textContent || '').includes(query);
        });
    };

    input.addEventListener('input', applySearch);
    applySearch();
});
