document.addEventListener('DOMContentLoaded', () => {
  const addressList = document.getElementById('address-list');
  const addressFormContainer = document.getElementById('address-form-container');
  const addressForm = document.getElementById('address-form');
  const addAddressBtn = document.getElementById('add-address-btn');

  // Fetch and display existing addresses
  fetch('/api/getAddresses')
    .then(response => response.json())
    .then(data => {
      data.addresses.forEach(address => {
        displayAddress(address);
      });
    });

  // Show the form when "Add New Address" is clicked
  addAddressBtn.addEventListener('click', () => {
    addressFormContainer.style.display = 'block';
    addressForm.reset();
  });

  // Handle form submission
  addressForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(addressForm);
    const address = Object.fromEntries(formData.entries());

    fetch('/api/saveAddress', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(address),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          displayAddress(address);
          addressFormContainer.style.display = 'none';
        }
      });
  });

  // Function to display an address
  function displayAddress(address) {
    const addressDiv = document.createElement('div');
    addressDiv.classList.add('address-item');
    addressDiv.innerHTML = `
      <p>${address.street}, ${address.locality}, ${address.city}, ${address.state}, ${address.country} - ${address.pincode}</p>
      <p>Phone: ${address.phone}</p>
      <button class="edit-address">Edit</button>
    `;
    addressList.appendChild(addressDiv);

    // Handle editing an address
    addressDiv.querySelector('.edit-address').addEventListener('click', () => {
      addressFormContainer.style.display = 'block';
      addressForm.street.value = address.street;
      addressForm.apartment.value = address.apartment || '';
      addressForm.landmark.value = address.landmark || '';
      addressForm.post.value = address.post || '';
      addressForm.locality.value = address.locality;
      addressForm.city.value = address.city;
      addressForm.pincode.value = address.pincode;
      addressForm.state.value = address.state;
      addressForm.country.value = address.country;
      addressForm.phone.value = address.phone;
    });
  }
});
