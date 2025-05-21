document.addEventListener('DOMContentLoaded', () => {
    const ordersContainer = document.getElementById('orders-container');

    const fetchOrders = async () => {
        try {
            const response = await fetch('../orders.json');
            const orders = await response.json();

            if (orders.length === 0) {
                ordersContainer.innerHTML = '<p>No orders found.</p>';
                return;
            }

            orders.forEach(order => {
                const orderDiv = document.createElement('div');
                orderDiv.classList.add('order-item');
                orderDiv.innerHTML = `
                    <h3>Order Date: ${order.orderDate}</h3>
                    <p><strong>Name:</strong> ${order.name}</p>
                    <p><strong>Phone:</strong> ${order.phone}</p>
                    <p><strong>Address:</strong> ${order.address}</p>
                    <p><strong>Payment Method:</strong> ${order.paymentMethod}</p>
                    <p><strong>Total Price:</strong> ₹${order.totalPrice}</p>
                    <p><strong>Delivery Date:</strong> ${order.deliveryDate}</p>
                    <h4>Products:</h4>
                    <ul>
                        ${order.products.map(product => `
                            <li>${product.name} - ₹${product.price} x ${product.quantity}</li>
                        `).join('')}
                    </ul>
                `;
                ordersContainer.appendChild(orderDiv);
            });
        } catch (error) {
            console.error('Error fetching orders:', error);
            ordersContainer.innerHTML = '<p>Failed to load orders. Please try again later.</p>';
        }
    };

    fetchOrders();
});
