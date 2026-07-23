const sidebar = document.querySelector('#adminSidebar');
const sidebarToggle = document.querySelector('#sidebarToggle');
const sidebarBackdrop = document.querySelector('#sidebarBackdrop');

const setSidebar = (open) => {
    if (!sidebar || !sidebarBackdrop || !sidebarToggle) return;
    sidebar.classList.toggle('open', open);
    sidebarBackdrop.classList.toggle('open', open);
    sidebarToggle.setAttribute('aria-expanded', String(open));
};

sidebarToggle?.addEventListener('click', () => setSidebar(!sidebar.classList.contains('open')));
sidebarBackdrop?.addEventListener('click', () => setSidebar(false));

for (const button of document.querySelectorAll('[data-password-toggle]')) {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
    });
}

const initializeImageInput = (input) => {
    if (input.dataset.imageReady === 'true') return;
    input.dataset.imageReady = 'true';

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        const preview = input.closest('.image-upload-row')?.querySelector('.image-preview');
        if (!file || !preview) return;

        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = 'Selected image preview';
        image.onload = () => URL.revokeObjectURL(image.src);
        preview.replaceChildren(image);
    });
};

for (const input of document.querySelectorAll('[data-image-input]')) {
    initializeImageInput(input);
}

const directChild = (collection, selector) => collection.querySelector(`:scope > ${selector}`);

const updateCollectionState = (collection) => {
    const itemsContainer = directChild(collection, '[data-collection-items]');
    const countBadge = collection.querySelector(':scope > .field-collection-heading [data-collection-count]');
    const emptyState = directChild(collection, '[data-collection-empty]');
    if (!itemsContainer) return;

    const items = [...itemsContainer.children].filter((element) => element.matches('[data-collection-item]'));

    items.forEach((item, index) => {
        const badge = item.querySelector(':scope > .collection-item-tools [data-collection-index]');
        const remove = item.querySelector(':scope > .collection-item-tools [data-remove-collection]');
        if (badge) badge.textContent = String(index + 1).padStart(2, '0');
        if (remove) remove.setAttribute('aria-label', `Remove item ${index + 1}`);
    });

    if (countBadge) {
        countBadge.textContent = `${items.length} ${items.length === 1 ? 'item' : 'items'}`;
    }

    if (emptyState) {
        emptyState.hidden = items.length > 0;
    }
};

const initializeCollection = (collection) => {
    if (collection.dataset.collectionReady === 'true') return;
    collection.dataset.collectionReady = 'true';

    const addButton = collection.querySelector(':scope > .field-collection-heading [data-add-collection]');
    const itemsContainer = directChild(collection, '[data-collection-items]');
    const template = directChild(collection, 'template[data-collection-template]');
    const indexToken = collection.dataset.indexToken;

    addButton?.addEventListener('click', () => {
        if (!itemsContainer || !template || !indexToken) return;

        const nextIndex = Number(collection.dataset.nextIndex || 0);
        const parser = document.createElement('template');
        parser.innerHTML = template.innerHTML.replaceAll(indexToken, String(nextIndex)).trim();
        const item = parser.content.firstElementChild;
        if (!item) return;

        collection.dataset.nextIndex = String(nextIndex + 1);
        itemsContainer.appendChild(item);

        for (const nestedCollection of item.querySelectorAll('[data-collection]')) {
            initializeCollection(nestedCollection);
        }

        for (const imageInput of item.querySelectorAll('[data-image-input]')) {
            initializeImageInput(imageInput);
        }

        updateCollectionState(collection);
        collection.closest('form')?.dispatchEvent(new Event('input', { bubbles: true }));
        item.querySelector('input, textarea')?.focus();
    });

    updateCollectionState(collection);
};

for (const collection of document.querySelectorAll('[data-collection]')) {
    initializeCollection(collection);
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-collection]');
    if (!button) return;

    const item = button.closest('[data-collection-item]');
    const collection = button.closest('[data-collection]');
    if (!item || !collection) return;

    item.remove();
    updateCollectionState(collection);
    collection.closest('form')?.dispatchEvent(new Event('input', { bubbles: true }));
});

for (const form of document.querySelectorAll('[data-dirty-form]')) {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
}
