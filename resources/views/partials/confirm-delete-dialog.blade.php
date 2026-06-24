<dialog id="modalConfirmDelete"
        class="app-dialog backdrop:bg-black/40 bg-transparent p-0 rounded-lg"
        style="--dialog-width: 28rem">
    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 id="confirmDeleteTitle" class="text-base font-semibold">Confirmar exclusão</h2>
            <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
        </div>
        <p id="confirmDeleteMessage" class="text-sm text-[#706f6c] mb-6"></p>
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
            <button type="button" data-dialog-close
                    class="px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                Cancelar
            </button>
            <button type="button" id="confirmDeleteSubmit"
                    class="px-4 py-2 sm:py-1.5 text-sm bg-[#f53003] text-white rounded-sm hover:bg-[#d42a02] transition-colors">
                Excluir
            </button>
        </div>
    </div>
</dialog>
