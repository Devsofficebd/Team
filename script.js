document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const noteCards = document.querySelectorAll('.note-card');

    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();

        noteCards.forEach(card => {
            const title = card.querySelector('h5')?.innerText.toLowerCase() || '';
            const content = card.querySelector('p')?.innerText.toLowerCase() || '';
            const tags = [...card.querySelectorAll('.tag-badge')].map(el => el.innerText.toLowerCase()).join(' ');

            const combined = title + ' ' + content + ' ' + tags;

            if (combined.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
