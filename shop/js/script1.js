document.addEventListener('DOMContentLoaded', function () {
    const products = document.querySelectorAll('.product');
    
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.2 });
    
    products.forEach(product => observer.observe(product));
  });

/*Product details nagbabago image kapag kinlick logic */
var MainImg = document.getElementById('Mainimg'); // Big image variable
var small = document.getElementsByClassName('smol-img'); // Small images collection

// Function to change the main image when a small image is clicked
function changeMainImage(index) {
    MainImg.src = small[index].src;
}

// Loop through all small images and add an event listener
for (let i = 0; i < small.length; i++) {
    small[i].onclick = function() {
        changeMainImage(i);
    };
}

document.getElementById("sizeSelector").addEventListener("change", function () /*funtion for details */
 {
    let selectedPrice = this.value;
    let priceDisplay = document.getElementById("price1");

    if (selectedPrice) {
        priceDisplay.textContent = `₱${selectedPrice}`;
    } else {
        priceDisplay.textContent = "₱399-599";
    }
});

document.getElementById('decrement').addEventListener('click', function() {
    var quantityElement = document.getElementById('quantity');
    var currentValue = parseInt(quantityElement.textContent);
    if (currentValue > 1) {
        quantityElement.textContent = currentValue - 1;
    }
});

document.getElementById('increment').addEventListener('click', function() {
    var quantityElement = document.getElementById('quantity');
    var currentValue = parseInt(quantityElement.textContent);
    if (currentValue < 100) { 
        quantityElement.textContent = currentValue + 1;
    }
});
