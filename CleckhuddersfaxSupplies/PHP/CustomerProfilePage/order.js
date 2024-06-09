document.addEventListener('DOMContentLoaded', function () {
    // Get the modal
    var modal = document.getElementById('reviewModal');

    // Get the <span> element that closes the modal
    var closeBtn = modal.querySelector('.close');

    // When the user clicks on a review button, open the modal
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('review-btn')) {
            var productName = event.target.getAttribute('data-product-name');
            var productId = event.target.getAttribute('data-product-id');
            var customerId = event.target.getAttribute('data-customer-id');

            document.getElementById('productName').textContent = productName;
            // Extract the order ID from the button's data attribute
            document.getElementById('reviewForm').setAttribute('data-product-id', productId);
            document.getElementById('reviewForm').setAttribute('data-customer-id', customerId);

            document.querySelectorAll('.star').forEach(star => star.classList.remove('fas'));
            document.querySelector('.star[data-rating="1"]').classList.add('fas'); 

            // Update the modal content or perform any other necessary actions based on the order ID
            modal.style.display = 'block';
        }
    });

    // When the user clicks on <span> (x), close the modal
    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // When the user submits the review form
    document.getElementById('reviewForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form submission

        // Retrieve form data
        var productId = this.getAttribute('data-product-id');
        var customerId = this.getAttribute('data-customer-id');
        var rating = document.getElementById('rating').value;
        var comments = document.getElementById('comments').value;




        // Send AJAX request to save the review
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_review.php'); // Replace 'save_review.php' with the URL of your PHP script
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Review saved successfully
                console.log('Review saved successfully');
                // Optionally, close the modal or show a success message
                modal.style.display = 'none';
            } else {
                // Error handling
                console.error('Error saving review');
            }
        };
        xhr.send('productId=' + encodeURIComponent(productId) + '&customerId=' + encodeURIComponent(customerId) + '&rating=' + encodeURIComponent(rating) + '&comments=' + encodeURIComponent(comments));
    });
});


// JavaScript for the star rating
const stars = document.querySelectorAll('.star');

stars.forEach(star => {
    star.addEventListener('click', () => {
        const ratingValue = parseInt(star.getAttribute('data-rating'));
        const starsContainer = document.getElementById('ratingStars');
        const stars = starsContainer.querySelectorAll('.star');

        // Remove the filled star class from all stars
        stars.forEach(s => s.classList.remove('fas'));
        // Add the filled star class to the selected star and all previous stars
        star.classList.add('fas');
        stars.forEach(s => {
            if (parseInt(s.getAttribute('data-rating')) <= ratingValue) {
                s.classList.add('fas');
            }
        });

        // Set the value of the hidden input field to the selected rating
        document.getElementById('rating').value = ratingValue;
    });
});
