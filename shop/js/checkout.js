document.addEventListener("DOMContentLoaded", function () {
    let orderedItems = JSON.parse(localStorage.getItem("checkoutCart")) || [];

    let cartBody = document.getElementById("checkout-items");
    cartBody.innerHTML = ""; // Clear existing rows before adding new ones

    orderedItems.forEach(product => {
        const newRow = document.createElement('tr');
        newRow.dataset.productId = product.id;
        newRow.classList.add("align-middle");

        newRow.innerHTML = `
            <td><img src="${product.image}" alt="${product.title}" style="width: 120px;"></td>
            <td class="text-start">${product.title}</td>
            <td>${product.size}</td>
            <td>${product.quantity}</td>
            <td>₱${product.price}</td>
        `;

        cartBody.appendChild(newRow);
    });
});