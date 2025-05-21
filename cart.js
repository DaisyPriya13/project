document.addEventListener("DOMContentLoaded", function () {
    const cartItemsContainer = document.getElementById("cart-items");
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = "<p>Your cart is empty.</p>";
    } else {
        cart.forEach((item, index) => {
            const itemElement = document.createElement("div");
            itemElement.classList.add("cart-item");

            itemElement.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <h3>${item.name}</h3>
                <p>${item.price}</p>
                <button class="remove-from-cart">Remove</button>
                <button class="buy-now" data-index="${index}">Buy</button>
            `;

            cartItemsContainer.appendChild(itemElement);
        });

        // Add event listeners to remove buttons
        document.querySelectorAll(".remove-from-cart").forEach((button, index) => {
            button.addEventListener("click", () => {
                cart.splice(index, 1);
                localStorage.setItem("cart", JSON.stringify(cart));
                location.reload(); // Reload the page to update the cart display
            });
        });

        // Add event listeners to buy buttons
        document.querySelectorAll(".buy-now").forEach((button) => {
            button.addEventListener("click", (event) => {
                const itemIndex = event.target.getAttribute("data-index");
                const selectedItem = cart[itemIndex];

                // Send cart data to the server
                fetch("scripts/save_cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(selectedItem),
                })
                    .then((response) => response.text())
                    .then((data) => {
                        alert(`Server Response: ${data}`);
                        // Redirect to a checkout page or handle the next steps
                        window.location.href = "checkout.html"; // Example redirect
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            });
        });
    }
});
