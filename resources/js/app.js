import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('task-list');
    if (!list) return;

    Sortable.create(list, {
        animation: 150,
        handle: '.drag-handle',
        onEnd() {
            const ids = [...list.querySelectorAll('[data-task-id]')]
                .map(el => parseInt(el.dataset.taskId, 10));

            fetch('/tasks/reorder', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ids }),
            }).catch(err => console.error('Reorder failed', err));
        },
    });
});
