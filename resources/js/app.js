

import Alpine from 'alpinejs';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

window.Alpine = Alpine;

const initRichEditors = () => {
    document.querySelectorAll('[data-quill-wrapper]').forEach((wrapper) => {
        if (wrapper.dataset.initialized === 'true') {
            return;
        }

        const toolbar = wrapper.querySelector('[data-quill-toolbar]');
        const editorElement = wrapper.querySelector('[data-quill-editor]');
        const input = wrapper.querySelector('[data-quill-input]');
        const statusElement = wrapper.querySelector('[data-upload-status]');
        const imageModal = wrapper.closest('.space-y-4')?.querySelector('[data-image-modal]');
        const uploadUrl = wrapper.dataset.uploadUrl;
        const csrfToken = wrapper.dataset.csrfToken;

        if (!toolbar || !editorElement || !input) {
            return;
        }

        const quill = new Quill(editorElement, {
            theme: 'snow',
            modules: {
                toolbar,
            },
            placeholder: 'Tulis isi artikel di sini...',
        });

        if (input.value) {
            quill.clipboard.dangerouslyPasteHTML(input.value);
        }

        wrapper.addEventListener('draft-restored', (event) => {
            quill.setContents([]);
            quill.clipboard.dangerouslyPasteHTML(event.detail?.content || '');
        });

        const sync = () => {
            input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        quill.on('text-change', sync);
        sync();

        const toolbarModule = quill.getModule('toolbar');
        let selectedImage = null;

        const showUploadStatus = (message, type = 'loading') => {
            if (!statusElement) {
                return;
            }

            statusElement.textContent = message;
            statusElement.className = 'rounded-xl px-4 py-3 text-xs font-semibold';
            statusElement.classList.add(...({
                loading: ['bg-sky-50', 'text-sky-700'],
                success: ['bg-emerald-50', 'text-emerald-700'],
                error: ['bg-rose-50', 'text-rose-700'],
            }[type] ?? ['bg-slate-50', 'text-slate-700']));
        };

        const uploadImage = async (file) => {
            if (!file?.type?.startsWith('image/')) {
                showUploadStatus('File yang dipilih bukan gambar.', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showUploadStatus('Ukuran gambar maksimal 5 MB.', 'error');
                return;
            }

            showUploadStatus(`Mengunggah ${file.name || 'gambar'}...`);

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    body: formData,
                });
                const payload = await response.json().catch(() => ({}));

                if (!response.ok || !payload.url) {
                    const validationMessage = payload.errors?.image?.[0];
                    throw new Error(validationMessage || payload.message || `Upload gagal dengan status ${response.status}.`);
                }

                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', payload.url, 'user');
                quill.setSelection(range.index + 1, 0);
                sync();
                showUploadStatus(payload.message || 'Gambar berhasil diunggah.', 'success');
            } catch (error) {
                showUploadStatus(error.message || 'Upload gambar gagal. Silakan coba kembali.', 'error');
            }
        };

        toolbarModule.addHandler('image', () => {
            const picker = document.createElement('input');
            picker.setAttribute('type', 'file');
            picker.setAttribute('accept', 'image/jpeg,image/png,image/gif,image/webp');
            picker.click();

            picker.addEventListener('change', () => {
                uploadImage(picker.files?.[0]);
            });
        });

        editorElement.addEventListener('paste', (event) => {
            const image = [...(event.clipboardData?.items ?? [])]
                .find((item) => item.type.startsWith('image/'))
                ?.getAsFile();

            if (image) {
                event.preventDefault();
                uploadImage(image);
            }
        });

        editorElement.addEventListener('dragover', (event) => {
            if ([...(event.dataTransfer?.items ?? [])].some((item) => item.type.startsWith('image/'))) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        editorElement.addEventListener('drop', (event) => {
            const image = [...(event.dataTransfer?.files ?? [])].find((file) => file.type.startsWith('image/'));

            if (image) {
                event.preventDefault();
                event.stopImmediatePropagation();
                uploadImage(image);
            }
        }, true);

        editorElement.addEventListener('click', (event) => {
            const image = event.target.closest('img');
            selectedImage = image;
            imageModal?.classList.toggle('hidden', !image);
            imageModal?.classList.toggle('flex', Boolean(image));
        });

        imageModal?.querySelectorAll('[data-image-size]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!selectedImage) {
                    return;
                }

                if (button.dataset.imageSize === 'original') {
                    selectedImage.removeAttribute('width');
                } else {
                    selectedImage.setAttribute('width', `${button.dataset.imageSize}%`);
                }
                selectedImage.removeAttribute('height');
                sync();
                imageModal.classList.add('hidden');
                imageModal.classList.remove('flex');
                showUploadStatus(`Ukuran gambar diatur ke ${button.dataset.imageSize === 'original' ? 'original' : `${button.dataset.imageSize}%`}.`, 'success');
            });
        });
        imageModal?.querySelector('[data-image-modal-close]')?.addEventListener('click', () => {
            imageModal.classList.add('hidden');
            imageModal.classList.remove('flex');
        });
        imageModal?.addEventListener('click', (event) => {
            if (event.target === imageModal) {
                imageModal.classList.add('hidden');
                imageModal.classList.remove('flex');
            }
        });
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !imageModal?.classList.contains('hidden')) {
                imageModal.classList.add('hidden');
                imageModal.classList.remove('flex');
            }
        });

        toolbarModule.addHandler('video', () => {
            const url = window.prompt('Masukkan URL YouTube:');

            if (!url) {
                return;
            }

            const embedUrl = toYoutubeEmbedUrl(url);

            if (!embedUrl) {
                window.alert('URL YouTube tidak valid.');
                return;
            }

            const range = quill.getSelection(true);
            quill.insertEmbed(range.index, 'video', embedUrl, 'user');
            quill.setSelection(range.index + 1, 0);
            sync();
        });

        wrapper.dataset.initialized = 'true';
    });
};

const toYoutubeEmbedUrl = (url) => {
    try {
        const parsed = new URL(url);

        if (parsed.hostname.includes('youtu.be')) {
            const id = parsed.pathname.replace('/', '');
            return id ? `https://www.youtube-nocookie.com/embed/${id}` : null;
        }

        if (parsed.hostname.includes('youtube.com')) {
            const id = parsed.searchParams.get('v');
            return id ? `https://www.youtube-nocookie.com/embed/${id}` : null;
        }
    } catch (error) {
        return null;
    }

    return null;
};

Alpine.data('articleFormPreview', ({
    initialTitle = '',
    initialContent = '',
    initialCategoryId = '',
    initialTags = [],
    categories = [],
    tags = [],
    authorName = '',
    draftKey = '',
    serverUpdatedAt = null,
}) => ({
    title: initialTitle,
    content: initialContent,
    categoryId: initialCategoryId,
    selectedTags: initialTags,
    categories,
    tags,
    authorName,
    mode: 'edit',
    categoryOpen: false,
    categorySearch: '',
    tagSearch: '',
    draftKey,
    serverUpdatedAt,
    draftSavedLabel: '',
    hasLocalDraft: false,
    draftSaveTimer: null,
    init() {
        this.restoreLocalDraft();

        this.$nextTick(() => {
            ['title', 'content', 'categoryId', 'selectedTags'].forEach((property) => {
                this.$watch(property, () => this.scheduleLocalDraftSave());
            });
        });
    },
    restoreLocalDraft() {
        if (!this.draftKey) {
            return;
        }

        try {
            const draft = JSON.parse(localStorage.getItem(this.draftKey));

            if (!draft || typeof draft !== 'object') {
                return;
            }

            if (this.serverUpdatedAt && draft.serverUpdatedAt !== this.serverUpdatedAt) {
                localStorage.removeItem(this.draftKey);
                return;
            }

            this.title = draft.title ?? this.title;
            this.content = draft.content ?? this.content;
            this.categoryId = draft.categoryId ?? this.categoryId;
            this.selectedTags = Array.isArray(draft.selectedTags) ? draft.selectedTags : this.selectedTags;
            this.hasLocalDraft = true;
            this.draftSavedLabel = this.formatDraftTime(draft.savedAt);
            this.$nextTick(() => {
                this.$root.querySelector('[data-quill-wrapper]')?.dispatchEvent(new CustomEvent('draft-restored', {
                    detail: { content: this.content },
                }));
            });
        } catch (error) {
            localStorage.removeItem(this.draftKey);
        }
    },
    scheduleLocalDraftSave() {
        window.clearTimeout(this.draftSaveTimer);
        this.draftSaveTimer = window.setTimeout(() => this.saveLocalDraft(), 700);
    },
    saveLocalDraft() {
        if (!this.draftKey) {
            return;
        }

        const savedAt = new Date().toISOString();
        try {
            localStorage.setItem(this.draftKey, JSON.stringify({
                title: this.title,
                content: this.content,
                categoryId: this.categoryId,
                selectedTags: this.selectedTags,
                savedAt,
                serverUpdatedAt: this.serverUpdatedAt,
            }));
            this.hasLocalDraft = true;
            this.draftSavedLabel = this.formatDraftTime(savedAt);
        } catch (error) {
            this.draftSavedLabel = 'gagal, penyimpanan browser penuh';
        }
    },
    clearLocalDraft() {
        window.clearTimeout(this.draftSaveTimer);
        localStorage.removeItem(this.draftKey);
        this.hasLocalDraft = false;
        this.draftSavedLabel = '';
    },
    discardLocalDraft() {
        if (!window.confirm('Hapus draft lokal dan muat ulang data terakhir yang tersimpan?')) {
            return;
        }

        this.clearLocalDraft();
        window.location.reload();
    },
    cancelChanges(url) {
        if (!window.confirm('Batalkan seluruh perubahan yang belum disimpan?')) {
            return;
        }

        this.clearLocalDraft();
        window.location.assign(url);
    },
    formatDraftTime(value) {
        if (!value) {
            return '';
        }

        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(value));
    },
    get categoryName() {
        return this.categories.find((category) => category.id === this.categoryId)?.name ?? '';
    },
    get selectedTagNames() {
        return this.tags
            .filter((tag) => this.selectedTags.includes(tag.id))
            .map((tag) => tag.name);
    },
    get filteredCategories() {
        const query = this.categorySearch.trim().toLocaleLowerCase('id-ID');
        return query ? this.categories.filter((category) => category.name.toLocaleLowerCase('id-ID').includes(query)) : this.categories;
    },
    get filteredTags() {
        const query = this.tagSearch.trim().toLocaleLowerCase('id-ID');
        return query ? this.tags.filter((tag) => tag.name.toLocaleLowerCase('id-ID').includes(query)) : this.tags;
    },
    selectCategory(id) {
        this.categoryId = id;
        this.categorySearch = '';
        this.categoryOpen = false;
    },
    get todayLabel() {
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).format(new Date());
    },
    get previewHtml() {
        return this.sanitizePreviewHtml(this.content) || '<p class="text-sm text-slate-400">Konten artikel akan tampil di sini setelah Anda mulai menulis.</p>';
    },
    sanitizePreviewHtml(html) {
        if (!html) {
            return '';
        }

        const doc = new DOMParser().parseFromString(html, 'text/html');
        doc.querySelectorAll('script, style, object, embed, form, input, button, textarea, select').forEach((node) => node.remove());

        doc.body.querySelectorAll('*').forEach((node) => {
            [...node.attributes].forEach((attribute) => {
                const name = attribute.name.toLowerCase();
                if (name.startsWith('on') || name === 'style') {
                    node.removeAttribute(attribute.name);
                }
            });
        });

        return doc.body.innerHTML;
    },
}));

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRichEditors);
} else {
    initRichEditors();
}
