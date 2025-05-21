document.addEventListener('DOMContentLoaded', () => {
  // Fetch user data and display it
  fetch('/api/getUserData')
    .then(response => response.json())
    .then(data => {
      document.getElementById('user-name').textContent = data.name;
      document.getElementById('user-email').textContent = data.email;
      document.getElementById('profile-picture').src = data.profilePicture || '../images/default-profile.png';
    });

  // Handle profile picture upload
  document.getElementById('save-picture').addEventListener('click', () => {
    const fileInput = document.getElementById('upload-picture');
    const formData = new FormData();
    formData.append('profilePicture', fileInput.files[0]);

    fetch('/api/uploadProfilePicture', {
      method: 'POST',
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('profile-picture').src = data.imageUrl;
          alert('Profile picture updated!');
        }
      });
  });

  // Fetch and display the address
  fetch('/api/getAddress')
    .then(response => response.json())
    .then(data => {
      document.getElementById('address').value = data.address;
    });
});

document.getElementById('info-form').addEventListener('submit', (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  fetch('/api/updateUserInfo', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Information updated!');
      }
    });
});

document.getElementById('add-address').addEventListener('click', () => {
  const address = prompt('Enter new address:');
  if (address) {
    fetch('/api/addAddress', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ address }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const addressList = document.getElementById('address-list');
          const newAddress = document.createElement('div');
          newAddress.textContent = address;
          addressList.appendChild(newAddress);
        }
      });
  }
});

// Save the address
document.getElementById('save-address').addEventListener('click', () => {
  const address = document.getElementById('address').value;

  fetch('/api/saveAddress', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ address }),
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Address saved successfully!');
      }
    });
});

// Fetch and display user data on page load
fetch('/api/getUserData')
  .then(response => response.json())
  .then(data => {
    document.getElementById('name').value = data.name;
    document.getElementById('email').value = data.email;
    document.getElementById('phone').value = data.phone;
    document.getElementById('profile-picture').src = data.profilePicture;

    const addressList = document.getElementById('address-list');
    data.addresses.forEach(address => {
      const addressDiv = document.createElement('div');
      addressDiv.textContent = address;
      addressList.appendChild(addressDiv);
    });

    const orderList = document.getElementById('order-list');
    data.orders.forEach(order => {
      const orderDiv = document.createElement('div');
      orderDiv.textContent = `Order #${order.id}: ${order.items.length} items`;
      orderList.appendChild(orderDiv);
    });
  });
