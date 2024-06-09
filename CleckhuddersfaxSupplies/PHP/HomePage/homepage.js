
var swiper = new Swiper(".product-slider", {
    loop:true,
    spaceBetween: 20,
    autoplay: {
        delay: 7500,
        disableOnInteraction: false,
    },
    centeredSlides: true,
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 2,
      },
      1020: {
        slidesPerView: 3,
      },
    },
});

// let body = document.querySelector('body');
// let OurProductsHTML = document.querySelector('.product-slider');

// let productSlider = [];


// const addDataToHTML = () => {
//   OurProductsHTML.innerHTML = '';
//   if(productSlider.length > 0){
//     productSlider.forEach(product => {
//           let newProduct = document.createElement('div');
//           newProduct.dataset.id = product.id;
//           newProduct.classList.add('box');
//           newProduct.innerHTML = `
//               <img src="${product.image}" alt="">
//               <h3>${product.name}</h3>
//               <div class="price">$${product.price}</div>
//               <div class="stars">
//                   <i class="fas fa-star"></i>
//                   <i class="fas fa-star"></i>
//                   <i class="fas fa-star"></i>
//                   <i class="fas fa-star"></i>
//                   <i class="fas fa-star-half-alt"></i>
//               </div>
//               <a href="#" class="btn">Add to Cart</a>
//           `;
//           OurProductsHTML.appendChild(newProduct);
//       })
//   }
// }

// const initApp = () => {
//   fetch('product.json')
//   .then(response => response.json())
//   .then(data => {
//     productSlider = data;
//       addDataToHTML();
//   })
// }
// initApp();