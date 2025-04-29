function updateTotal() {
    const inputs = document.querySelectorAll('.order-item input[type="number"]');
    let total = 0;

    inputs.forEach(input => {
        const cantidad = parseInt(input.value) || 0;
        const precio = parseFloat(input.parentElement.textContent.match(/\$([\d.]+)/)[1]);
        total += cantidad * precio;
    });

    document.getElementById('total').textContent = total.toFixed(2);
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', updateTotal);