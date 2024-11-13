document.addEventListener("DOMContentLoaded", function () {
    const productForm = document.getElementById("productForm");
    const qrContainer = document.getElementById("qrContainer");

    let product = {
        name: '',
        quantity: '',
        productCode: '',
        location: '',
        qrCode: ''
    };

    // Generate QR Code from product code
    const generateQrCode = async (productCode) => {
        if (productCode) {
            try {
                const qrCodeDataUrl = await QRCode.toDataURL(productCode);
                qrContainer.innerHTML = `<img src="${qrCodeDataUrl}" alt="QR Code">`;
                product.qrCode = qrCodeDataUrl;
            } catch (err) {
                console.error('Error generating QR code:', err);
            }
        }
    };

    // Get user's location
    const getLocation = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const { latitude, longitude } = position.coords;
                product.location = `${latitude},${longitude}`;
                console.log('Location:', product.location);
            }, function (error) {
                console.log("Geolocation error:", error);
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        product[name] = value;

        if (name === 'productCode') {
            generateQrCode(value); 
        }
    };

    const handleFormSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await fetch("../backend/api/create_prod.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(product),
            });

            const data = await response.json();

            if (data.status === 'success') {
                alert("Product added successfully!");
                productForm.reset();
                qrContainer.innerHTML = '';
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error('Error creating product:', err);
            alert("Error creating product.");
        }
    };

    // Event listeners
    productForm.addEventListener('input', handleInputChange);
    productForm.addEventListener('submit', handleFormSubmit);

    // Get location when page loads
    getLocation();
});


// Register the service worker
// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('../service-worker.js')
//     .then(function(registration) {
//         console.log('Service Worker registered with scope:', registration.scope);
//     }).catch(function(error) {
//         console.log('Service Worker registration failed:', error);
//     });
// }
