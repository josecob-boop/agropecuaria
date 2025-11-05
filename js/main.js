// ===============================================
// LÓGICA DEL PUNTO DE VENTA (POS)
// ===============================================

let cart = {}; // Objeto para almacenar los productos en el carrito {id: {nombre, precio, cantidad, stock_max}}
const TAX_RATE = 0.13; // Tasa de impuesto (ej. 13%)

// DOM Elements
const cartBody = document.getElementById('cart-body');
const subtotalElement = document.getElementById('subtotal');
const taxElement = document.getElementById('tax');
const grandTotalElement = document.getElementById('grand-total');
const cartDataInput = document.getElementById('cart-data');
const totalAmountInput = document.getElementById('total-amount');
const finalizeSaleButton = document.getElementById('finalize-sale');


/**
 * Añade un producto al carrito o incrementa su cantidad.
 * @param {number} id - ID del producto.
 * @param {string} nombre - Nombre del producto.
 * @param {number} precio - Precio unitario.
 * @param {number} stock_max - Stock máximo disponible.
 */
function addItemToCart(id, nombre, precio, stock_max) {
    if (cart[id]) {
        // Si el producto ya está, incrementar cantidad, respetando el stock
        if (cart[id].cantidad < stock_max) {
            cart[id].cantidad++;
        } else {
            alert(`No se puede añadir más de ${nombre}. Stock máximo alcanzado (${stock_max}).`);
        }
    } else {
        // Si es un producto nuevo, añadirlo
        cart[id] = {
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            stock_max: stock_max
        };
    }
    updateCartDisplay();
}

/**
 * Elimina un ítem del carrito (cantidad a cero) o reduce su cantidad.
 * @param {number} id - ID del producto.
 * @param {string} action - 'add', 'subtract', o 'remove'.
 */
function updateCartItem(id, action) {
    if (!cart[id]) return;

    if (action === 'subtract') {
        cart[id].cantidad--;
    } else if (action === 'add') {
         if (cart[id].cantidad < cart[id].stock_max) {
            cart[id].cantidad++;
        } else {
            alert(`Stock máximo alcanzado (${cart[id].stock_max}).`);
        }
    } else if (action === 'remove') {
        cart[id].cantidad = 0; // Se eliminará en el siguiente paso
    }
    
    // Eliminar si la cantidad es cero o menos
    if (cart[id].cantidad <= 0) {
        delete cart[id];
    }

    updateCartDisplay();
}

/**
 * Actualiza la tabla HTML del carrito y recalcula los totales.
 */
function updateCartDisplay() {
    let subtotal = 0;
    let html = '';

    for (const id in cart) {
        const item = cart[id];
        const itemSubtotal = item.precio * item.cantidad;
        subtotal += itemSubtotal;

        html += `
            <tr>
                <td>${item.nombre}</td>
                <td>
                    <button type="button" onclick="updateCartItem(${id}, 'subtract')">-</button>
                    ${item.cantidad}
                    <button type="button" onclick="updateCartItem(${id}, 'add')">+</button>
                </td>
                <td>$${itemSubtotal.toFixed(2)}</td>
                <td>
                    <button type="button" onclick="updateCartItem(${id}, 'remove')" class="btn-sm-danger">X</button>
                </td>
            </tr>
        `;
    }

    // Si el carrito está vacío
    if (Object.keys(cart).length === 0) {
        html = '<tr><td colspan="4" style="text-align:center;">Agrega productos al carrito.</td></tr>';
        finalizeSaleButton.disabled = true;
    } else {
        finalizeSaleButton.disabled = false;
    }

    // Insertar el HTML
    cartBody.innerHTML = html;

    // Recalcular Totales
    const tax = subtotal * TAX_RATE;
    const grandTotal = subtotal + tax;

    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    taxElement.textContent = `$${tax.toFixed(2)}`;
    grandTotalElement.textContent = `$${grandTotal.toFixed(2)}`;

    // Preparar datos para el envío a PHP
    prepareCheckoutData(grandTotal);
}

/**
 * Prepara los datos finales del carrito en formato JSON para el backend.
 * @param {number} grandTotal - Total de la venta.
 */
function prepareCheckoutData(grandTotal) {
    // Convertir el objeto 'cart' en un array de ítems para un JSON más limpio
    const cartArray = Object.keys(cart).map(id => ({
        id: parseInt(id),
        nombre: cart[id].nombre,
        precio: cart[id].precio,
        cantidad: cart[id].cantidad
    }));

    // Insertar el JSON y el total en los campos ocultos del formulario
    cartDataInput.value = JSON.stringify(cartArray);
    totalAmountInput.value = grandTotal.toFixed(2);
}

/**
 * Filtra los productos visibles en el catálogo rápido.
 */
function filterProducts() {
    const input = document.getElementById('search-product');
    const filter = input.value.toUpperCase();
    const productList = document.getElementById('product-list');
    const products = productList.getElementsByClassName('product-card-pos');

    for (let i = 0; i < products.length; i++) {
        const name = products[i].getAttribute('data-nombre');
        if (name.toUpperCase().indexOf(filter) > -1) {
            products[i].style.display = "";
        } else {
            products[i].style.display = "none";
        }
    }
}

// Inicializar la vista del carrito al cargar la página
window.onload = updateCartDisplay;
