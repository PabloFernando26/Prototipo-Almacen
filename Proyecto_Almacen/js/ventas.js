
function selectPayment(paymentType) {
    document.getElementById('metodo_pago').value = paymentType;
    let options = document.querySelectorAll('.payment-option');
    options.forEach(option => option.classList.remove('selected'));
    document.querySelector(`[onclick="selectPayment('${paymentType}')"]`).classList.add('selected');
}

document.getElementById('confirmarVentaForm').addEventListener('submit', function (event) {
    const metodoPago = document.getElementById('metodo_pago').value;

    // Solo validar si el método de pago es Débito o Crédito
    if (metodoPago === 'Débito' || metodoPago === 'Crédito') {
        const numeroTarjeta = document.getElementById('numero_tarjeta').value;
        const fechaExpiracion = document.getElementById('fecha_expiracion').value;

        // Validación del número de tarjeta
        if (!validateCardNumber(numeroTarjeta)) {
            alert('Número de tarjeta inválido. Debe contener 16 dígitos.');
            event.preventDefault(); // Evitar el envío del formulario
            return;
        }

        // Validación de la fecha de expiración
        if (!validateExpiryDate(fechaExpiracion)) {
            alert('Fecha de expiración inválida. Formato: MM/AA.');
            event.preventDefault(); // Evitar el envío del formulario
            return;
        }

        // Aquí podrías agregar más validaciones, como verificar el RUT si es necesario
    }
});

// Función para validar el número de tarjeta
function validateCardNumber(number) {
    // Quitar caracteres no numéricos
    number = number.replace(/\D/g, '');
    return number.length === 16; // Validar que tenga 16 dígitos
}

// Función para validar la fecha de expiración
function validateExpiryDate(date) {
    const regex = /^(0[1-9]|1[0-2])\/\d{2}$/; // Formato MM/AA
    if (!regex.test(date)) {
        return false; // No coincide con el formato
    }
    
    const [month, year] = date.split('/').map(Number);
    const expiryDate = new Date(2000 + year, month - 1);
    const today = new Date();

    return expiryDate >= today; // La fecha no puede ser en el pasado
}
