function showContent(section) {
    const contentContainer = document.getElementById('content-container');
    const customerDetailsSection = document.getElementById('customer-details');
    const traderDetailsSection = document.getElementById('trader-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderRequestsSection = document.getElementById('trader-requests');
    const shopRequestsSection = document.getElementById('shop-requests');

    switch (section) {
        case 'customer':
            // Show customer details section and hide others
            contentContainer.style.display = 'none';
            customerDetailsSection.style.display = 'block';
            traderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderRequestsSection.style.display = 'none';
            shopRequestsSection.style.display = 'none';
            break;
        case 'traders':
            // Show trader details section and hide others
            contentContainer.style.display = 'none';
            customerDetailsSection.style.display = 'none';
            traderDetailsSection.style.display = 'block';
            productDetailsSection.style.display = 'none';
            traderRequestsSection.style.display = 'none';
            shopRequestsSection.style.display = 'none';
            break;
        case 'products':
            // Show product details section and hide others
            contentContainer.style.display = 'none';
            customerDetailsSection.style.display = 'none';
            traderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'block';
            traderRequestsSection.style.display = 'none';
            shopRequestsSection.style.display = 'none';
            break;
        case 'request':
            // Show request details section and hide others
            contentContainer.style.display = 'none';
            customerDetailsSection.style.display = 'none';
            traderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderRequestsSection.style.display = 'block';
            shopRequestsSection.style.display = 'none';
            break;
        case 'Shopreuest':
            // Show request details section and hide others
            contentContainer.style.display = 'none';
            customerDetailsSection.style.display = 'none';
            traderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderRequestsSection.style.display = 'none';
            shopRequestsSection.style.display = 'block';
            break;
        default:
            // Default behavior
            contentContainer.style.display = 'flex';
            customerDetailsSection.style.display = 'none';
            traderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderRequestsSection.style.display = 'none';
            shopRequestsSection.style.display = 'none';
    }
}

document.getElementById('dashboard-button').addEventListener('click', function() {
    // Show all boxes when Admin Dashboard is clicked
    const contentContainer = document.getElementById('content-container');
    const customerDetailsSection = document.getElementById('customer-details');
    const traderDetailsSection = document.getElementById('trader-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderRequestsSection = document.getElementById('trader-requests');
    const shopRequestsSection = document.getElementById('shop-requests'); 

    contentContainer.style.display = 'flex';
    customerDetailsSection.style.display = 'none';
    traderDetailsSection.style.display = 'none';
    productDetailsSection.style.display = 'none';
    traderRequestsSection.style.display = 'none'; 
    shopRequestsSection.style.display = 'none';
});

// Show all content by default when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const contentContainer = document.getElementById('content-container');
    const customerDetailsSection = document.getElementById('customer-details');
    const traderDetailsSection = document.getElementById('trader-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderRequestsSection = document.getElementById('trader-requests');
    const shopRequestsSection = document.getElementById('shop-requests'); 

    contentContainer.style.display = 'flex';
    customerDetailsSection.style.display = 'none';
    traderDetailsSection.style.display = 'none';
    productDetailsSection.style.display = 'none';
    traderRequestsSection.style.display = 'none';
    shopRequestsSection.style.display = 'none';
});