document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JavaScript Loaded');

    // --- Common Elements & Utility ---
    function displayMessage(elementOrId, message, isSuccess = true) {
        const element = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
        if (element) {
            element.innerHTML = `<p style="color:${isSuccess ? 'green' : 'red'};">${message}</p>`;
            setTimeout(() => { if(element) element.innerHTML = ''; }, 5000);
        } else {
            console.warn("displayMessage: Element not found for ID:", elementOrId, "Message:", message);
        }
    }

    function htmlspecialchars(str) {
        if (typeof str !== 'string') return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // --- Categories Management (from previous step, assumed to be here and correct) ---
    const categoryForm = document.getElementById('categoryForm');
    const categoryFormTitle = document.getElementById('categoryFormTitle');
    const categoryFormMessages = document.getElementById('categoryFormMessages');
    const categoriesTableContainer = document.getElementById('categoriesTableContainer');
    const showAddCategoryFormBtn = document.getElementById('showAddCategoryFormBtn');
    const categoryIdField = document.getElementById('category_id_form');
    const categoryNameField = document.getElementById('category_name');
    const categoryDescriptionField = document.getElementById('category_description');
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const cancelEditCategoryBtn = document.getElementById('cancelEditCategoryBtn');
    const categoryFormContainer = document.getElementById('categoryFormContainer');
    const productCategorySelect = document.getElementById('category_id'); // In product form

    async function loadCategories() {
        const isOnCategoriesPage = !!categoriesTableContainer;
        const isOnProductsPage = !!productCategorySelect;
        console.log('Loading categories...');

        try {
            const response = await fetch('php/api/categories_manage.php', { method: 'GET' });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
            const result = await response.json();

            if (result.status === 'success' && Array.isArray(result.data)) {
                if (isOnCategoriesPage) {
                    if (result.data.length === 0) {
                        categoriesTableContainer.innerHTML = '<p>No categories found. Add some!</p>';
                    } else {
                        let tableHTML = '<table border="1" style="width:100%; border-collapse: collapse; margin-top:15px;"><thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Created At</th><th>Actions</th></tr></thead><tbody>';
                        result.data.forEach(cat => {
                            tableHTML += `<tr>
                                <td>${cat.category_id}</td>
                                <td>${cat.category_name ? htmlspecialchars(cat.category_name) : ''}</td>
                                <td>${cat.category_description ? htmlspecialchars(cat.category_description) : 'N/A'}</td>
                                <td>${new Date(cat.created_at).toLocaleString()}</td>
                                <td>
                                    <button type="button" class="editCategoryBtn" data-id="${cat.category_id}">Edit</button>
                                    <button type="button" class="deleteCategoryBtn" data-id="${cat.category_id}">Delete</button>
                                </td>
                            </tr>`;
                        });
                        tableHTML += '</tbody></table>';
                        categoriesTableContainer.innerHTML = tableHTML;
                        attachCategoryActionListeners();
                    }
                }
                if (isOnProductsPage) {
                    productCategorySelect.innerHTML = '<option value="">Select a Category</option>';
                    result.data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.category_id;
                        option.textContent = htmlspecialchars(cat.category_name);
                        productCategorySelect.appendChild(option);
                    });
                }
            } else if (result.status === 'success' && result.data.length === 0 && isOnCategoriesPage) {
                 categoriesTableContainer.innerHTML = '<p>No categories found. Add one!</p>';
            } else if(result.status !== 'success') {
                if(isOnCategoriesPage) displayMessage(categoriesTableContainer, result.message || 'Could not load categories.', false);
                console.error("API error loading categories:", result.message);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            if(isOnCategoriesPage) displayMessage(categoriesTableContainer, `Failed to load categories: ${error.message}`, false);
        }
    }

    async function handleCategoryFormSubmit(event) {
        event.preventDefault();
        if (!categoryForm || !categoryFormMessages) return;
        const category_id_val = categoryIdField.value; // Use a different var name
        const category_name_val = categoryNameField.value;
        const category_description_val = categoryDescriptionField.value;
        if (!category_name_val.trim()) {
            displayMessage(categoryFormMessages, 'Category name is required.', false);
            return;
        }
        const isUpdating = !!category_id_val;
        const url = 'php/api/categories_manage.php';
        const method = isUpdating ? 'PUT' : 'POST';
        const body = JSON.stringify({
            category_id: isUpdating ? category_id_val : undefined,
            category_name: category_name_val,
            category_description: category_description_val
        });
        try {
            const response = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json' }, body: body });
            const result = await response.json();
            if (result.status === 'success') {
                displayMessage(categoryFormMessages, result.message || 'Category saved successfully!');
                categoryForm.reset();
                categoryIdField.value = '';
                categoryFormTitle.textContent = 'Add New Category';
                saveCategoryBtn.textContent = 'Save Category';
                cancelEditCategoryBtn.style.display = 'none';
                if(categoryFormContainer) categoryFormContainer.style.display = 'none';
                loadCategories();
            } else {
                displayMessage(categoryFormMessages, result.message || `Failed to save category. Status: ${response.status}`, false);
            }
        } catch (error) {
            console.error('Error saving category:', error);
            displayMessage(categoryFormMessages, `Error: ${error.message}`, false);
        }
    }

    async function editCategory(id) {
        if (!categoryFormContainer || !categoryForm || !categoryFormTitle || !saveCategoryBtn || !cancelEditCategoryBtn || !categoryIdField || !categoryNameField || !categoryDescriptionField || !categoryFormMessages) return;
        categoryForm.reset();
        categoryFormMessages.innerHTML = '';
        categoryFormContainer.style.display = 'block';
        categoryFormTitle.textContent = 'Edit Category';
        saveCategoryBtn.textContent = 'Update Category';
        cancelEditCategoryBtn.style.display = 'inline-block';
        try {
            const response = await fetch(`php/api/categories_manage.php?category_id=${id}`, { method: 'GET' });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const result = await response.json();
            if (result.status === 'success' && result.data) {
                categoryIdField.value = result.data.category_id;
                categoryNameField.value = result.data.category_name;
                categoryDescriptionField.value = result.data.category_description || '';
                categoryNameField.focus();
            } else {
                displayMessage(categoryFormMessages, result.message || 'Failed to fetch category details.', false);
                if(categoryFormContainer) categoryFormContainer.style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching category for edit:', error);
            displayMessage(categoryFormMessages, `Error: ${error.message}`, false);
            if(categoryFormContainer) categoryFormContainer.style.display = 'none';
        }
    }

    async function deleteCategory(id) {
        if (!confirm(`Are you sure you want to delete category ID ${id}? This cannot be undone and might affect products using this category.`)) return;
        try {
            const response = await fetch(`php/api/categories_manage.php?category_id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            if (result.status === 'success') {
                const globalMessages = document.getElementById('globalAdminMessages') || categoryFormMessages; // Prefer global if exists
                displayMessage(globalMessages, result.message || 'Category deleted successfully!');
                loadCategories();
            } else {
                displayMessage(categoryFormMessages, result.message || `Failed to delete category. Status: ${response.status}`, false);
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            displayMessage(categoryFormMessages, `Error: ${error.message}`, false);
        }
    }

    function attachCategoryActionListeners() {
        document.querySelectorAll('.editCategoryBtn').forEach(button => button.addEventListener('click', (e) => editCategory(e.target.dataset.id)));
        document.querySelectorAll('.deleteCategoryBtn').forEach(button => button.addEventListener('click', (e) => deleteCategory(e.target.dataset.id)));
    }

    if (showAddCategoryFormBtn && categoryFormContainer && categoryForm) {
        showAddCategoryFormBtn.addEventListener('click', () => {
            categoryFormContainer.style.display = 'block'; categoryForm.reset(); categoryFormTitle.textContent = 'Add New Category';
            saveCategoryBtn.textContent = 'Save Category'; cancelEditCategoryBtn.style.display = 'none';
            categoryIdField.value = ''; categoryFormMessages.innerHTML = ''; categoryNameField.focus();
        });
    }
    if (cancelEditCategoryBtn && categoryFormContainer && categoryForm) {
        cancelEditCategoryBtn.addEventListener('click', () => {
            categoryFormContainer.style.display = 'none'; categoryForm.reset();
            categoryIdField.value = ''; categoryFormMessages.innerHTML = '';
        });
    }

    // --- Products Management ---
    const productForm = document.getElementById('productForm');
    const productFormTitle = document.getElementById('productFormTitle');
    const productFormMessages = document.getElementById('productFormMessages');
    const productsTableContainer = document.getElementById('productsTableContainer');
    const showAddProductFormBtn = document.getElementById('showAddProductFormBtn');
    const productIdField = document.getElementById('product_id'); // Hidden input in product form
    const productNameField = document.getElementById('product_name');
    const productDescriptionField = document.getElementById('description');
    const productPriceField = document.getElementById('price');
    // productCategorySelect already defined under Categories section
    const productStockField = document.getElementById('stock_available');
    const productFileField = document.getElementById('product_file');
    const productCoverImageField = document.getElementById('cover_image');
    const currentProductFileDisplay = document.getElementById('currentProductFile');
    const currentCoverImageDisplay = document.getElementById('currentCoverImagePreview'); // Matches example HTML
    const saveProductBtn = document.getElementById('saveProductBtn');
    const cancelEditProductBtn = document.getElementById('cancelEditProductBtn'); // In product form
    const productFormContainer = document.getElementById('productFormContainer');

    async function loadProducts() {
        console.log('Loading products...');
        if (!productsTableContainer) return;

        try {
            const response = await fetch('php/api/products_read.php', { method: 'GET' });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
            const result = await response.json();

            if (result.status === 'success' && Array.isArray(result.data)) {
                 if (result.data.length === 0) {
                        productsTableContainer.innerHTML = '<p>No products found. Add some!</p>';
                    } else {
                        let tableHTML = '<table border="1" style="width:100%; border-collapse: collapse; margin-top:15px;"><thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Stock</th><th>Cover</th><th>Actions</th></tr></thead><tbody>';
                        result.data.forEach(prod => {
                            tableHTML += `<tr>
                                <td>${prod.product_id}</td>
                                <td>${htmlspecialchars(prod.product_name)}</td>
                                <td>${parseFloat(prod.price).toFixed(2)}</td>
                                <td>${prod.category_name ? htmlspecialchars(prod.category_name) : 'N/A'}</td>
                                <td>${prod.stock_available === null ? 'Unlimited' : prod.stock_available}</td>
                                <td>${prod.cover_image_path ? `<img src="${htmlspecialchars(prod.cover_image_path)}" alt="${htmlspecialchars(prod.product_name)}" width="50" height="50" style="object-fit:cover;">` : 'No image'}</td>
                                <td>
                                    <button type="button" class="editProductBtn" data-id="${prod.product_id}">Edit</button>
                                    <button type="button" class="deleteProductBtn" data-id="${prod.product_id}">Delete</button>
                                </td>
                            </tr>`;
                        });
                        tableHTML += '</tbody></table>';
                        productsTableContainer.innerHTML = tableHTML;
                        attachProductActionListeners();
                    }
            } else if (result.status === 'success' && result.data.length === 0) {
                 productsTableContainer.innerHTML = '<p>No products found. Add one!</p>';
            }
            else { // API error or unexpected structure
                displayMessage(productsTableContainer, result.message || 'Could not load products.', false);
                console.error("API error loading products:", result.message);
            }
        } catch (error) {
            console.error('Error loading products:', error);
            displayMessage(productsTableContainer, `Failed to load products: ${error.message}`, false);
        }
    }

    async function handleProductFormSubmit(event) {
        event.preventDefault();
        if (!productForm || !productFormMessages) return;

        const formData = new FormData(productForm);
        const isUpdating = !!formData.get('product_id'); // product_id is the name of the hidden input

        // Basic client-side validation
        if (!formData.get('product_name').trim() || !formData.get('description').trim() || !formData.get('price') || !formData.get('category_id')) {
            displayMessage(productFormMessages, 'Name, description, price, and category are required.', false);
            return;
        }
        if (!isUpdating && !formData.get('product_file').name) {
             displayMessage(productFormMessages, 'Product file is required for new products.', false);
            return;
        }
        if (!isUpdating && !formData.get('cover_image').name) {
             displayMessage(productFormMessages, 'Cover image is required for new products.', false);
            return;
        }

        const apiEndpoint = isUpdating ? 'php/api/products_update.php' : 'php/api/products_create.php';

        try {
            const response = await fetch(apiEndpoint, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                displayMessage(productFormMessages, result.message || 'Product saved successfully!');
                productForm.reset();
                if(productIdField) productIdField.value = ''; // Clear hidden ID
                if(productFormTitle) productFormTitle.textContent = 'Add New Product';
                if(saveProductBtn) saveProductBtn.textContent = 'Save Product';
                if(cancelEditProductBtn) cancelEditProductBtn.style.display = 'none';
                if(productFormContainer) productFormContainer.style.display = 'none';
                if(currentProductFileDisplay) currentProductFileDisplay.textContent = '';
                if(currentCoverImageDisplay) currentCoverImageDisplay.innerHTML = ''; // Clear image preview

                loadProducts(); // Refresh product list
            } else {
                 let errorMsg = result.message || 'Failed to save product.';
                 if (result.errors && Array.isArray(result.errors)) {
                    errorMsg += '<ul>' + result.errors.map(e => `<li>${htmlspecialchars(e)}</li>`).join('') + '</ul>';
                 }
                displayMessage(productFormMessages, errorMsg, false);
            }
        } catch (error) {
            console.error('Error saving product:', error);
            displayMessage(productFormMessages, `Error: ${error.message}`, false);
        }
    }

    async function editProduct(id) {
        if (!productFormContainer || !productForm || !productFormTitle || !saveProductBtn || !cancelEditProductBtn || !productIdField || !productNameField || !productDescriptionField || !productPriceField || !productCategorySelect || !productStockField || !currentProductFileDisplay || !currentCoverImageDisplay || !productFormMessages) return;

        productForm.reset();
        productFormMessages.innerHTML = '';
        productFormContainer.style.display = 'block';
        productFormTitle.textContent = 'Edit Product';
        saveProductBtn.textContent = 'Update Product';
        cancelEditProductBtn.style.display = 'inline-block';
        currentProductFileDisplay.textContent = '';
        currentCoverImageDisplay.innerHTML = '';

        try {
            const response = await fetch(`php/api/products_read.php?product_id=${id}`, { method: 'GET' });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const result = await response.json();

            if (result.status === 'success' && result.data) {
                const p = result.data;
                productIdField.value = p.product_id;
                productNameField.value = p.product_name;
                productDescriptionField.value = p.description;
                productPriceField.value = p.price;
                productCategorySelect.value = p.category_id; // Assumes categories are already loaded and populated in select
                productStockField.value = p.stock_available === null ? '' : p.stock_available;

                if (p.file_path) currentProductFileDisplay.textContent = `Current file: ${htmlspecialchars(p.file_path.split('/').pop())}. Leave empty to keep current.`;
                if (p.cover_image_path) {
                    currentCoverImageDisplay.innerHTML = `Current: <img src="${htmlspecialchars(p.cover_image_path)}" alt="Cover Preview" style="max-width: 100px; max-height: 100px; object-fit: cover; vertical-align: middle; margin-right: 5px;"> <small>${htmlspecialchars(p.cover_image_path.split('/').pop())}</small>. Leave empty to keep current.`;
                } else {
                     currentCoverImageDisplay.innerHTML = 'No current cover image.';
                }
                productNameField.focus();
            } else {
                displayMessage(productFormMessages, result.message || 'Failed to fetch product details.', false);
                if(productFormContainer) productFormContainer.style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching product for edit:', error);
            displayMessage(productFormMessages, `Error: ${error.message}`, false);
            if(productFormContainer) productFormContainer.style.display = 'none';
        }
    }

    async function deleteProduct(id) {
        if (!confirm(`Are you sure you want to delete product ID ${id}? This will also delete associated files and cannot be undone.`)) return;

        try {
            const response = await fetch(`php/api/products_delete.php`, {
                method: 'POST', // API expects POST with product_id in body
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: id })
            });
            const result = await response.json();

            if (result.status === 'success') {
                const globalMessages = document.getElementById('globalAdminMessages') || productFormMessages;
                displayMessage(globalMessages, result.message || 'Product deleted successfully!');
                loadProducts(); // Refresh list
            } else {
                displayMessage(productFormMessages, result.message || `Failed to delete product. Status: ${response.status}`, false);
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            displayMessage(productFormMessages, `Error: ${error.message}`, false);
        }
    }

    function attachProductActionListeners() {
        document.querySelectorAll('.editProductBtn').forEach(button => button.addEventListener('click', (e) => editProduct(e.target.dataset.id)));
        document.querySelectorAll('.deleteProductBtn').forEach(button => button.addEventListener('click', (e) => deleteProduct(e.target.dataset.id)));
    }

    if (showAddProductFormBtn && productFormContainer && productForm) {
        showAddProductFormBtn.addEventListener('click', () => {
            productFormContainer.style.display = 'block'; productForm.reset(); productFormTitle.textContent = 'Add New Product';
            saveProductBtn.textContent = 'Save Product'; cancelEditProductBtn.style.display = 'none';
            productIdField.value = ''; productFormMessages.innerHTML = '';
            currentProductFileDisplay.textContent = ''; currentCoverImageDisplay.innerHTML = '';
            productNameField.focus();
        });
    }

    if (cancelEditProductBtn && productFormContainer && productForm) { // This is for product form
        cancelEditProductBtn.addEventListener('click', () => {
            productFormContainer.style.display = 'none'; productForm.reset();
            productIdField.value = ''; productFormMessages.innerHTML = '';
            currentProductFileDisplay.textContent = ''; currentCoverImageDisplay.innerHTML = '';
        });
    }

    // --- Initializations ---
    if (window.location.pathname.includes('admin_categories.php')) {
        loadCategories(); // Load and display categories table
        if (categoryForm) categoryForm.addEventListener('submit', handleCategoryFormSubmit);
    }

    if (window.location.pathname.includes('admin_products.php')) {
        loadCategories(); // This will populate the product category dropdown
        loadProducts();   // Load and display products table
        if (productForm) productForm.addEventListener('submit', handleProductFormSubmit);
    }
});
