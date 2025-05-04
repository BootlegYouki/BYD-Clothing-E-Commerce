/**
 * Payment Return Handler
 * 
 * This script handles the logic after returning from payment gateway:
 * 1. Checks payment status from URL parameters
 * 2. Clears cart only if payment was successful
 * 3. Updates UI based on payment status
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get payment status from URL
    const urlParams = new URLSearchParams(window.location.search);
    const paymentStatus = urlParams.get('status');
    const referenceNumber = urlParams.get('reference');
    
    console.log('Payment status:', paymentStatus);
    console.log('Reference number:', referenceNumber);
    
    // If payment was successful, clear the cart
    if (paymentStatus === 'success') {
        console.log('Payment successful - clearing cart');
        
        // Clear both localStorage cart keys
        localStorage.removeItem('shopping-cart');
        localStorage.removeItem('cart');
        localStorage.removeItem('pending_payment');
        
        // Display success message
        const successMsgElement = document.getElementById('payment-success-message');
        if (successMsgElement) {
            successMsgElement.classList.remove('d-none');
            
            // Add reference number to message if available
            const refNumElement = document.getElementById('reference-number');
            if (refNumElement && referenceNumber) {
                refNumElement.textContent = referenceNumber;
            }
        }
    } else {
        // Payment failed or was canceled
        console.log('Payment failed or canceled - keeping cart items');
        
        // Get stored reference
        const storedReference = localStorage.getItem('order_reference') || 
                               sessionStorage.getItem('order_reference');
        
        // Display failure message
        const failureMsgElement = document.getElementById('payment-failure-message');
        if (failureMsgElement) {
            failureMsgElement.classList.remove('d-none');
            
            // Add reference number to message if available
            const refNumElement = document.getElementById('failed-reference-number');
            if (refNumElement && (referenceNumber || storedReference)) {
                refNumElement.textContent = referenceNumber || storedReference || 'Not available';
            }
        }
        
        // Clean up localStorage/sessionStorage reference but keep cart
        localStorage.removeItem('pending_payment');
    }
    
    // Clean up reference from storage (regardless of payment status)
    localStorage.removeItem('order_reference');
    sessionStorage.removeItem('order_reference');
    
    // Update cart count in the header (if function exists)
    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }
});
