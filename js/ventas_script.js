// Este código debe ir en un archivo js/ventas_script.js o en el script tag de formulario_ventas.php

let carrito = {}; // Objeto global para almacenar los productos en el carrito
let resultadoBusqueda = {};

// ------------------------------------------------------------------
// FUNCIONES DE BÚSQUEDA Y SUGERENCIAS (NUEVAS CARACTERÍSTICAS)
// ------------------------------------------------------------------

/**
 * Llama a la API para obtener sugerencias de productos basadas en el término de búsqueda.
 */
function obtenerSugerencias() {
    console.log("EVENTO KEYUP DISPARADO")
    const input = document.getElementById('codigoBarra');
    const searchTerm = input.value.trim();
    const resultadosDiv = document.getElementById('resultadoBusqueda');
    resultadosDiv.innerHTML = ''; 
    resultadosBusqueda = {};

    // Esperar al menos 3 caracteres para iniciar la sugerencia
    if (searchTerm.length < 2) return; 

    // Llamada AJAX a la nueva API de sugerencias
    fetch(`api_sugerencias.php?term=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const ul = document.createElement('ul');
                ul.className = 'sugerencias-lista';

                data.forEach(producto => {
                    // Almacenar el objeto completo por ID
                    resultadoBusqueda[producto.ProductoID] = producto;
                    
                    const li = document.createElement('li');
                    li.textContent = `${producto.Nombre} ($${parseFloat(producto.PrecioVenta).toFixed(2)}) - Stock: ${producto.Stock}`;
                    li.dataset.id = producto.ProductoID;
                    li.onclick = seleccionarProducto;
                    ul.appendChild(li);
                });
                resultadosDiv.appendChild(ul);
            }
        })
        .catch(error => console.error('Error en sugerencias:', error));
}

/**
 * Maneja la selección de un producto de la lista de sugerencias.
 */
function seleccionarProducto(event) {
    const productoID = event.target.dataset.id;
    const productoSeleccionado = resultadosBusqueda[productoID];
    
    if (productoSeleccionado) {
        // Usa la función principal para agregar el producto al carrito
        agregarProductoACarrito(productoSeleccionado);
    }
    
    // Limpiar y ocultar la lista después de seleccionar
    document.getElementById('codigoBarra').value = ''; 
    document.getElementById('codigoBarra').focus();
    document.getElementById('resultadoBusqueda').innerHTML = '';
}

// Función para buscar el producto (simulado con AJAX)
function buscarProducto(event) {
    event.preventDefault(); // Evita que el formulario se envíe realmente
    const codigoBarra = document.getElementById('codigoBarra').value.trim();
    if (!codigoBarra) return;

    // 1. Petición AJAX al servidor para obtener datos del producto
    fetch(`api_productos.php?codigo=${codigoBarra}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                agregarProductoACarrito(data);
            }
            document.getElementById('codigoBarra').value = ''; // Limpiar campo
            document.getElementById('codigoBarra').focus();
            document.getElementById('resultadoBusqueda').innerHTML = '';
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
            alert('Error al conectar con el servidor.');
        });
}

// Función para agregar o incrementar producto en el carrito
function agregarProductoACarrito(producto) {
    const id = producto.ProductoID;
    
    if (carrito[id]) {
        // Producto ya en el carrito: aumentar cantidad
        if (carrito[id].cantidad < producto.Stock) {
            carrito[id].cantidad++;
        } else {
            alert("Stock insuficiente. Cantidad máxima alcanzada.");
        }
    } else {
        // Nuevo producto: agregar
        if (producto.Stock > 0) {
            carrito[id] = {
                id: producto.ProductoID,
                nombre: producto.Nombre,
                precio: parseFloat(producto.PrecioVenta),
                stock: producto.Stock,
                imagen: producto.Imagen,
                cantidad: 1,
            };
        } else {
            alert("Producto sin stock.");
        }
    }
    actualizarCarritoDOM();
}

// Función para renderizar y calcular todo
function actualizarCarritoDOM() {
    const tbody = document.getElementById('tablaCarrito').querySelector('tbody');
    tbody.innerHTML = '';
    let subtotalGeneral = 0;

    for (const id in carrito) {
        const item = carrito[id];
        const subtotalItem = item.cantidad * item.precio;
        subtotalGeneral += subtotalItem;

        const newRow = tbody.insertRow();
        newRow.innerHTML = `
            <td>${item.nombre}</td>
            <td><img src="${item.imagen}" style="width: 50px;"></td>
            <td>$${item.precio.toFixed(2)}</td>
            <td>
                <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
                       onchange="modificarCantidad(${item.id}, this.value)" style="width: 60px;">
            </td>
            <td>$${subtotalItem.toFixed(2)}</td>
            <td>
                <button onclick="eliminarItem(${item.id})">X</button>
            </td>
        `;
    }

    // Actualizar Totales
    document.getElementById('subtotalVenta').textContent = subtotalGeneral.toFixed(2);
    document.getElementById('totalPagar').textContent = subtotalGeneral.toFixed(2);
    
    // Actualizar Campos ocultos para PHP
    document.getElementById('inputTotalVenta').value = subtotalGeneral.toFixed(2);
    document.getElementById('inputDetallesJSON').value = JSON.stringify(carrito);
    
    calcularCambio(); // Recalcular cambio siempre que cambie el total
}

// Función para modificar la cantidad desde el input
function modificarCantidad(id, nuevaCantidad) {
    const cantidad = parseInt(nuevaCantidad);
    const item = carrito[id];
    
    if (isNaN(cantidad) || cantidad <= 0) {
        eliminarItem(id);
        return;
    }
    
    if (cantidad > item.stock) {
        alert("Cantidad excede el stock disponible (" + item.stock + ")");
        item.cantidad = item.stock;
    } else {
        item.cantidad = cantidad;
    }
    
    actualizarCarritoDOM();
}

// Función para eliminar un producto del carrito
function eliminarItem(id) {
    delete carrito[id];
    actualizarCarritoDOM();
}

// Función para calcular el cambio
function calcularCambio() {
    const total = parseFloat(document.getElementById('totalPagar').textContent) || 0;
    const pago = parseFloat(document.getElementById('pagoCliente').value) || 0;
    
    const cambio = pago - total;
    
    document.getElementById('cambioDevolver').textContent = cambio.toFixed(2);
    document.getElementById('inputCambio').value = cambio.toFixed(2);
}

// Validación final antes de enviar
function validarVenta() {
    if (Object.keys(carrito).length === 0) {
        alert("El carrito está vacío. Agregue productos.");
        return false;
    }
    const pago = parseFloat(document.getElementById('pagoCliente').value) || 0;
    const total = parseFloat(document.getElementById('totalPagar').textContent) || 0;

    if (pago < total) {
        alert("El pago del cliente es insuficiente.");
        return false;
    }
    return true;
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    actualizarCarritoDOM();
    
    // Asignar evento 'keyup' para disparar la sugerencia al escribir
    const inputBusqueda = document.getElementById('codigoBarra');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', obtenerSugerencias);
    }else{
        console.error("Error_ Elemento 'codigoBarra' no encontrado en el DOM.");
    }
});