// Helper Functions


const toggleCheckbox = document.getElementById('dark-toggle');
        const htmlElement = document.documentElement;

        // Check if the user has a saved theme preference in localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            htmlElement.classList.add(savedTheme);
            toggleCheckbox.checked = savedTheme === 'dark'; // Set the checkbox state
        }

        // Listen for toggle changes
        toggleCheckbox.addEventListener('change', () => {
            if (toggleCheckbox.checked) {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark'); // Save dark mode preference
            } else {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light'); // Save light mode preference
            }
        });

function loadParkingSlots() {
    const parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || Array(50).fill(null);
    const parkingGrid = document.getElementById('parking-grid');

    parkingGrid.innerHTML = ''; // Clear existing content

    const carNumbers = []; // Track unique cars

    // Loop through all 50 slots and render them
    parkingSlots.forEach((slot, index) => {
        const slotDiv = document.createElement('div');
        slotDiv.className = `slot p-4 rounded shadow-md ${slot ? 'bg-red-500' : 'bg-green-500'}`;
        slotDiv.id = `slot${index}`;
        
        slotDiv.innerHTML = `<div class="slot-number">${index + 1}</div>`; // Slot number outside the box

        if (slot && !carNumbers.includes(slot.carNumber)) {
            carNumbers.push(slot.carNumber);
            slotDiv.innerHTML += `
                <p align="center">${slot.carName} (${slot.carNumber})</p>
                <button class="mt-2" onclick="checkout(${index})">Checkout</button>
            `;
        } else if (!slot) {
            slotDiv.innerHTML += `<p align="center">Available</p>`;
        }

        parkingGrid.appendChild(slotDiv); // Append to the grid
    });
}

function checkout(slotIndex) {
    const parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || [];
    const cars = JSON.parse(localStorage.getItem('cars')) || [];

    if (parkingSlots[slotIndex]) {
        const car = parkingSlots[slotIndex];
        const checkOutTime = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });
        const parkedDuration = new Date() - new Date(car.checkinTime);
        const parkedHours = Math.floor(parkedDuration / (1000 * 60 * 60));
        const ratePerHour = 20; // Change rate as needed
        const totalAmount = parkedHours * ratePerHour;

        const index = cars.findIndex(c => c.carNumber === car.carNumber);
        if (index !== -1) {
            cars.splice(index, 1); // Remove checked-out car
        }

        parkingSlots[slotIndex] = null; // Mark slot as available

        localStorage.setItem('cars', JSON.stringify(cars));
        localStorage.setItem('parkingSlots', JSON.stringify(parkingSlots));

        alert(`Checkout successful! Total Amount: ₹${totalAmount}\nCheck-out Time: ${checkOutTime}`);
        loadParkingSlots(); // Reload parking slots
    } else {
        alert('Slot is already empty!');
    }
}

function displayCarList() {
    const carList = document.getElementById('carList');
    const parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || Array(50).fill(null);
    carList.innerHTML = ''; // Clear existing data

    parkingSlots.forEach((slot, index) => {
        if (slot) {
            const row = `
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="py-3 px-6 text-left">${index + 1}</td>
                    <td class="py-3 px-6 text-left">${slot.carNumber}</td>
                    <td class="py-3 px-6 text-left">${slot.carName}</td>
                    <td class="py-3 px-6 text-left">${slot.carType}-Wheeler</td>
                    <td class="py-3 px-6 text-left">${slot.mobileNumber}</td>
                    <td class="py-3 px-6 text-left">${slot.checkinTime}</td>
                    <td class="py-3 px-6 text-center">
                        <button class="checkout bg-red-500 text-white px-4 py-2 rounded" data-slot="${index}">
                            Checkout
                        </button>
                    </td>
                </tr>
            `;
            carList.insertAdjacentHTML('beforeend', row);
        }
    });
}

function updateReport() {
    if (typeof (Storage) !== 'undefined') {
        if (window.localStorage) {
            window.localStorage.setItem('updateReport', new Date().toISOString());
        }
    }
}

function checkoutCar(slotIndex) {
    const parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || [];
    const slot = parkingSlots[slotIndex];
    const checkinTime = new Date(slot.checkinTime).getTime();
    const checkoutTime = new Date().getTime();
    const durationHours = (checkoutTime - checkinTime) / (1000 * 60 * 60);
    const ratePerHour = 50;
    const bill = Math.round(durationHours * ratePerHour);

    alert(`Car Number ${slot.carNumber} checked out. Bill: ₹${bill}`);
    parkingSlots[slotIndex] = null; // Free the parking slot
    localStorage.setItem('parkingSlots', JSON.stringify(parkingSlots));

    updateReport(); // Update report page
    displayCarList(); // Refresh the list
}

function carEntryFormHandler(event) {
    event.preventDefault();

    const carNumber = document.getElementById('car-number').value;
    const carName = document.getElementById('car-name').value;
    const carType = document.getElementById('car-type').value;
    const mobileNumber = document.getElementById('mobile-number').value;
    const checkinTime = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });

    let cars = JSON.parse(localStorage.getItem('cars')) || [];
    let parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || Array(50).fill(null);

    const availableSlot = parkingSlots.indexOf(null);

    if (availableSlot !== -1) {
        const tokenNumber = 'T' + new Date().getTime();
        parkingSlots[availableSlot] = { carNumber, carName, carType, mobileNumber, checkinTime, slot: availableSlot, tokenNumber };
        cars.push(parkingSlots[availableSlot]);

        localStorage.setItem('cars', JSON.stringify(cars));
        localStorage.setItem('parkingSlots', JSON.stringify(parkingSlots));

        document.getElementById('tokenNumber').textContent = tokenNumber;
        document.getElementById('checkinTime').textContent = checkinTime;
        document.getElementById('vehicleNumber').textContent = carNumber;
        document.getElementById('confirmationModal').style.display = 'block';

        setTimeout(() => {
            window.location.href = 'dashboard.html';
        }, 7000);
    } else {
        alert('No parking slots available!');
    }
}

function init() {
    const carList = document.getElementById('carList');
    const form = document.getElementById('carEntryForm');
    const modal = document.getElementById('confirmationModal');

    if (form) {
        form.addEventListener('submit', carEntryFormHandler);
    }

    if (carList) {
        carList.addEventListener('click', function (event) {
            if (event.target.classList.contains('checkout')) {
                const slotIndex = event.target.getAttribute('data-slot');
                checkoutCar(slotIndex);
            }
        });

        displayCarList(); // Initial call to display car list
    }

    if (modal) {
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    }

    document.addEventListener('DOMContentLoaded', loadParkingSlots);
}

init(); // Initialize the event listeners and page loading actions
