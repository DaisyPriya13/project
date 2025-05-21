document.addEventListener('DOMContentLoaded', () => {
    const checkoutContainer = document.getElementById('checkout-container');
    const checkoutTotal = document.getElementById('checkout-total');
    const checkoutForm = document.getElementById('checkout-form');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const singleProduct = JSON.parse(localStorage.getItem('singleProduct')) || null;

    const renderCheckoutItems = () => {
        checkoutContainer.innerHTML = '';
        let total = 0;

        const itemsToRender = singleProduct ? [singleProduct] : cart;

        itemsToRender.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('checkout-item');
            itemDiv.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div>
                    <p><strong>${item.name}</strong></p>
                    ${item.scent ? `<p>Scent: ${item.scent}</p>` : ''}
                    <p>Price: ₹${item.price.toFixed(2)}</p>
                    <p>Quantity: ${item.quantity}</p>
                </div>
            `;
            checkoutContainer.appendChild(itemDiv);
            total += item.price * item.quantity;
        });

        checkoutTotal.textContent = total.toFixed(2);
    };

    const calculateDeliveryDate = () => {
        const today = new Date();
        const deliveryDate = new Date(today);
        deliveryDate.setDate(today.getDate() + 7); // Add 7 working days
        return deliveryDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD
    };

    checkoutForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const name = document.getElementById('name').value;
        const phone = document.getElementById('phone').value;
        const address = document.getElementById('address').value;
        const paymentMethod = document.getElementById('payment-method').value;
        const deliveryDate = calculateDeliveryDate();

        const orderDetails = {
            name,
            phone,
            address,
            paymentMethod,
            products: singleProduct ? [singleProduct] : cart,
            totalPrice: checkoutTotal.textContent,
            orderDate: new Date().toISOString().split('T')[0],
            deliveryDate,
        };

        // Send order details to the backend
        fetch('http://localhost:3000/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderDetails),
        })
            .then(response => response.json())
            .then(data => {
                alert(`Order placed successfully! Your order will be delivered by ${deliveryDate}.`);
                localStorage.removeItem('cart');
                localStorage.removeItem('singleProduct');
                window.location.href = '../html/home.html';
            })
            .catch(error => {
                console.error('Error placing order:', error);
                alert('Failed to place order. Please try again.');
            });
    });

    renderCheckoutItems();
});
