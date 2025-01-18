window.validarCedula = function(parametro) {
    var cedula = parametro;

    // Comprobamos si la cédula tiene exactamente 10 caracteres
    if (cedula.length == 10) {
        var digito_region = cedula.substring(0, 2);

        // Verificar si la región es válida (Ecuador tiene 24 regiones)
        if (digito_region >= 1 && digito_region <= 24) {

            var ultimo_digito = cedula.substring(9, 10);

            // Sumar los dígitos de las posiciones impares y pares según la regla de validación
            var pares = parseInt(cedula.substring(1, 2)) + parseInt(cedula.substring(3, 4)) +
                        parseInt(cedula.substring(5, 6)) + parseInt(cedula.substring(7, 8));

            var numero1 = parseInt(cedula.substring(0, 1)) * 2;
            if (numero1 > 9) numero1 -= 9;

            var numero3 = parseInt(cedula.substring(2, 3)) * 2;
            if (numero3 > 9) numero3 -= 9;

            var numero5 = parseInt(cedula.substring(4, 5)) * 2;
            if (numero5 > 9) numero5 -= 9;

            var numero7 = parseInt(cedula.substring(6, 7)) * 2;
            if (numero7 > 9) numero7 -= 9;

            var numero9 = parseInt(cedula.substring(8, 9)) * 2;
            if (numero9 > 9) numero9 -= 9;

            var impares = numero1 + numero3 + numero5 + numero7 + numero9;

            // Sumar los números impares y pares
            var suma_total = pares + impares;

            // Calcular el dígito validador
            var primer_digito_suma = String(suma_total).substring(0, 1);
            var decena = (parseInt(primer_digito_suma) + 1) * 10;
            var digito_validador = decena - suma_total;

            if (digito_validador == 10) digito_validador = 0;

            // Comparar el dígito validador con el último dígito de la cédula
            if (digito_validador != ultimo_digito) {
                return false; // Cédula inválida
            } else {
                return true; // Cédula válida
            }

        } else {
            return false; // Cédula inválida (región no válida)
        }
    } else {
        return false; // Cédula inválida (no tiene 10 caracteres)
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const cedulaInput = document.getElementById('student_form_cedula');

    // Verifica si el campo de cédula existe en el DOM
    if (!cedulaInput) {
        console.error('El campo de cédula no se encuentra en el DOM.');
        return;
    }

    form.addEventListener('submit', function (event) {
        console.log('Validando cédula: ', cedulaInput.value);

        // Evitar el envío del formulario si la cédula no es válida
        if (!validarCedula(cedulaInput.value)) {
            event.preventDefault();
            alert('La cédula ingresada no es válida. Por favor, verifica los datos.');
        } else {
            console.log('Cédula válida');
        }
    });
});

