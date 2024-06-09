function showContent(section) {
    const contentContainer = document.getElementById('content-container');
    const shopDetailsSection = document.getElementById('shop-details');
    const orderDetailsSection = document.getElementById('order-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderProfileSection = document.getElementById('trader-profile');

    switch (section) {
        
        case 'traderprofile':
            traderProfileSection.style.display = 'block';
            contentContainer.style.display = 'none';
            shopDetailsSection.style.display = 'none';
            orderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            break;
        case 'shops':
            contentContainer.style.display = 'none';
            shopDetailsSection.style.display = 'block';
            orderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderProfileSection.style.display = 'none';
            break;
        case 'orders':
            contentContainer.style.display = 'none';
            shopDetailsSection.style.display = 'none';
            orderDetailsSection.style.display = 'block';
            productDetailsSection.style.display = 'none';
            traderProfileSection.style.display = 'none';
            break;
        case 'products':
            contentContainer.style.display = 'none';
            shopDetailsSection.style.display = 'none';
            orderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'block';
            traderProfileSection.style.display = 'none';
            break;
        default:
            // Default behavior
            contentContainer.style.display = 'flex';
            shopDetailsSection.style.display = 'none';
            orderDetailsSection.style.display = 'none';
            productDetailsSection.style.display = 'none';
            traderProfileSection.style.display = 'none';
    }
}


document.getElementById('dashboard-button').addEventListener('click', function() {
    // Show all boxes when Admin Dashboard is clicked
    const contentContainer = document.getElementById('content-container');
    const shopDetailsSection = document.getElementById('shop-details');
    const orderDetailsSection = document.getElementById('order-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderProfileSection = document.getElementById('trader-profile');

    contentContainer.style.display = 'flex';
    shopDetailsSection.style.display = 'none';
    orderDetailsSection.style.display = 'none';
    productDetailsSection.style.display = 'none';
    traderProfileSection.style.display = 'none';
});

// Show all content by default when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const contentContainer = document.getElementById('content-container');
    const shopDetailsSection = document.getElementById('shop-details');
    const orderDetailsSection = document.getElementById('order-details');
    const productDetailsSection = document.getElementById('product-details');
    const traderProfileSection = document.getElementById('trader-profile');

    contentContainer.style.display = 'flex';
    shopDetailsSection.style.display = 'none';
    orderDetailsSection.style.display = 'none';
    productDetailsSection.style.display = 'none';
    traderProfileSection.style.display = 'none';
});

// Get the modal
var modal = document.getElementById("updateShopModal");

// Get the button that opens the modal
var btns = document.querySelectorAll(".update-btn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btns.forEach(function(btn) {
    btn.onclick = function() {
        modal.style.display = "block";
    }
});

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}


    // Get the modal for updating product details
var updateProductModal = document.getElementById("updateProductModal");

// Get all the buttons with class "upd-btn" (Update buttons)
var updateButtons = document.querySelectorAll(".upd-btn");

// Loop through each "Update" button and add an event listener
updateButtons.forEach(function(btn) {
    btn.addEventListener("click", function() {
        // Show the update product modal
        updateProductModal.style.display = "block";
    });
});

// Get the <span> element that closes the update product modal
var closeUpdateProductModal = document.getElementsByClassName("close-update")[0];

// When the user clicks on <span> (x), close the update product modal
closeUpdateProductModal.onclick = function() {
    updateProductModal.style.display = "none";
}

// When the user clicks anywhere outside of the update product modal, close it
window.onclick = function(event) {
    if (event.target == updateProductModal) {
        updateProductModal.style.display = "none";
    }
}


// Get the modal
var addProductModal = document.getElementById("addProductModal");

// Get the button that opens the modal
var addProductBtn = document.getElementById("add-product-btn");

// Get the <span> element that closes the modal
var closeAddProductBtn = document.getElementsByClassName("close-add")[0];

// When the user clicks the button, open the modal
addProductBtn.onclick = function() {
  addProductModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
closeAddProductBtn.onclick = function() {
  addProductModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == addProductModal) {
    addProductModal.style.display = "none";
  }
}

