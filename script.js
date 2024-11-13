function domReady(fn) {
    if (
        document.readyState === "complete" ||
        document.readyState === "interactive"
    ) {
        setTimeout(fn, 1000);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

domReady(function () {
    // If found you qr code
    function onScanSuccess(decodeText, decodeResult) {
        alert("You Qr is : " + decodeText, decodeResult);
    }

    let htmlscanner = new Html5QrcodeScanner(
        "my-qr-reader",
        { fps: 10, qrbos: 250 }
    );
    htmlscanner.render(onScanSuccess);
});

// Register the service worker
// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('/service-worker.js')
//     .then(function(registration) {
//         console.log('Service Worker registered with scope:', registration.scope);
//     }).catch(function(error) {
//         console.log('Service Worker registration failed:', error);
//     });
// }
