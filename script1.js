var MainImg = document.getElementById('Mainimg');
var small = document.getElementsByClassName('smol-img');

small[0].onclick =function(){
    MainImg.src = small[0].src;
}

small[1].onclick =function(){
    MainImg.src = small[1].src;
}
small[2].onclick =function(){
    MainImg.src = small[2].src;
}
small[3].onclick =function(){
    MainImg.src = small[3].src;
}

document.getElementById("sizeSelector").addEventListener("change", function () {
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
    if (currentValue < 100) { // Assuming 100 is the max quantity
        quantityElement.textContent = currentValue + 1;
    }
});
