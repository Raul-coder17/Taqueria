function toggleSubcategoria() {
    const categoria = document.getElementById('categoria').value;
    const subcategoria = document.getElementById('subcategoria');
    const subcategoriaLabel = document.getElementById('subcategoria-label');

    if (categoria === 'Bebidas') {
        subcategoria.style.display = 'block';
        subcategoriaLabel.style.display = 'block';
        subcategoria.required = true;
    } else {
        subcategoria.style.display = 'none';
        subcategoriaLabel.style.display = 'none';
        subcategoria.required = false;
    }
}

// Ejecutar al cargar la página para establecer el estado inicial
document.addEventListener('DOMContentLoaded', toggleSubcategoria);