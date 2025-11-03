/**
 * PhotoPreview - класс для предпросмотра и управления фотографиями
 */
class PhotoPreview {
    constructor(options) {
        this.input = options.input;
        this.preview = options.preview;
        this.mainIndexInput = options.mainIndexInput;
        this.maxFiles = options.maxFiles || 10;
        this.maxFileSize = options.maxFileSize || 5 * 1024 * 1024; // 5MB
        this.allowedTypes = options.allowedTypes || ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        this.files = [];
        this.mainIndex = 0;

        this.init();
    }

    init() {
        if (!this.input || !this.preview) {
            console.error('PhotoPreview: input или preview элементы не найдены');
            return;
        }

        this.bindEvents();
        this.updatePreview();
    }

    // Синхронизируем текущие выбранные файлы с реальным input.files,
    // чтобы они отправились на сервер при сабмите формы
    syncInputFiles() {
        try {
            const dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            this.input.files = dt.files;
        } catch (e) {
            console.warn('PhotoPreview: syncInputFiles failed', e);
        }
    }

    bindEvents() {
        this.input.addEventListener('change', (e) => {
            this.handleFileSelect(e.target.files);
        });

        // Drag & Drop события
        this.preview.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.preview.classList.add('drag-over');
        });

        this.preview.addEventListener('dragleave', (e) => {
            e.preventDefault();
            this.preview.classList.remove('drag-over');
        });

        this.preview.addEventListener('drop', (e) => {
            e.preventDefault();
            this.preview.classList.remove('drag-over');
            this.handleFileSelect(e.dataTransfer.files);
            this.syncInputFiles();
        });
    }

    handleFileSelect(fileList) {
        const newFiles = Array.from(fileList);

        // Проверяем лимит файлов
        if (this.files.length + newFiles.length > this.maxFiles) {
            alert(`Максимальное количество файлов: ${this.maxFiles}`);
            return;
        }

        // Валидируем каждый файл
        for (let file of newFiles) {
            if (!this.validateFile(file)) {
                continue;
            }
            this.files.push(file);
        }

        this.updatePreview();
        this.syncInputFiles();
    }

    validateFile(file) {
        // Проверка типа файла
        if (!this.allowedTypes.includes(file.type)) {
            alert(`Файл ${file.name} имеет недопустимый формат. Разрешены: ${this.allowedTypes.join(', ')}`);
            return false;
        }

        // Проверка размера файла
        if (file.size > this.maxFileSize) {
            alert(`Файл ${file.name} слишком большой. Максимальный размер: ${this.maxFileSize / 1024 / 1024}MB`);
            return false;
        }

        return true;
    }

    updatePreview() {
        this.preview.innerHTML = '';

        if (this.files.length === 0) {
            return;
        }

        this.files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.createPreviewItem(e.target.result, index, file.name);
            };
            reader.readAsDataURL(file);
        });
    }

    createPreviewItem(imageSrc, index, fileName) {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-lg-3 mb-3';

        const isMain = index === this.mainIndex;

        col.innerHTML = `
            <div class="card photo-preview-card">
                <div class="position-relative">
                    <img src="${imageSrc}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${fileName}">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="photoPreview.removeFile(${index})" title="Удалить фото">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body p-2">
                    <button type="button" class="btn btn-sm ${isMain ? 'btn-primary' : 'btn-outline-primary'} w-100" onclick="photoPreview.setMain(${index})">
                        ${isMain ? '<i class="fas fa-star me-1"></i>Основное' : 'Сделать основным'}
                    </button>
                    <small class="text-muted d-block mt-1">${fileName}</small>
                </div>
            </div>
        `;

        this.preview.appendChild(col);
    }

    removeFile(index) {
        this.files.splice(index, 1);

        // Если удаляем основную фотографию, устанавливаем первую как основную
        if (index === this.mainIndex) {
            this.mainIndex = 0;
        } else if (index < this.mainIndex) {
            this.mainIndex--;
        }

        this.updatePreview();
        this.updateMainIndexInput();
        this.syncInputFiles();
    }

    setMain(index) {
        this.mainIndex = index;
        this.updatePreview();
        this.updateMainIndexInput();
    }

    updateMainIndexInput() {
        if (this.mainIndexInput) {
            this.mainIndexInput.value = this.mainIndex;
        }
    }

    enableDragDrop() {
        // Drag & Drop уже включен в init()
        console.log('PhotoPreview: Drag & Drop включен');
    }

    getFiles() {
        return this.files;
    }

    clear() {
        this.files = [];
        this.mainIndex = 0;
        this.updatePreview();
        this.updateMainIndexInput();
        this.syncInputFiles();
    }
}

// Глобальная переменная для доступа из других скриптов
window.PhotoPreview = PhotoPreview;
