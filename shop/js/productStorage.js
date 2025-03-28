document.addEventListener("DOMContentLoaded", function () {
    const products = document.querySelectorAll(".product a");
    
    products.forEach(product => {
        product.addEventListener("click", function (event) {
            event.preventDefault();
            
            const imgSrc = this.querySelector("img").src;
            const title = this.parentElement.querySelector("h4").innerText;
            const price = this.parentElement.querySelector("p").innerText;
            
            localStorage.setItem("selectedProductImg", imgSrc);
            localStorage.setItem("selectedProductTitle", title);
            localStorage.setItem("selectedProductPrice", price);
            
            window.location.href = "details.php";
        });
    });
});

// On details.html
if (window.location.pathname.includes("details.php")) {
    document.addEventListener("DOMContentLoaded", function () {
        const mainImg = document.getElementById("Mainimg");
        const titleElement = document.querySelector(".sproduct h4"); 
        const priceElement = document.getElementById("price1");
        const descElement = document.getElementById("desc1");
        const titleElement1 = document.getElementById("title1"); 
        
        const savedImg = localStorage.getItem("selectedProductImg");
        const savedTitle = localStorage.getItem("selectedProductTitle");
        const savedPrice = localStorage.getItem("selectedProductPrice");

        if (mainImg && savedImg) mainImg.src = savedImg;
        if (titleElement && savedTitle) titleElement.innerText = savedTitle;
        if (priceElement && savedPrice) priceElement.innerText = savedPrice;

        console.log("Loaded Image:", savedImg);
        
        const productImages = {
            "EROS": ["img/t-shirt_details/eros_a.webp", "img/t-shirt_details/eros_b.webp", "img/t-shirt_details/eros_c.webp"],
            "GAVIN": ["img/t-shirt_details/gavin_a.webp", "img/t-shirt_details/gavin_b.webp", "img/t-shirt_details/gavin_c.webp"],
            "GINO": ["img/t-shirt_details/gino_a.webp", "img/t-shirt_details/gino_b.webp", "img/t-shirt_details/gino_c.webp"],
            "BRAD": ["img/t-shirt_details/brad_A.webp", "img/t-shirt_details/brad_B.webp", "img/t-shirt_details/brad_C.webp"],
            "ARON": ["img/t-shirt_details/aron_a.webp", "img/t-shirt_details/aron_b.webp", "img/t-shirt_details/aron_c.webp"],
            "MEDI": ["img/t-shirt_details/medi_a.webp", "img/t-shirt_details/medi_b.webp", "img/t-shirt_details/medi_c.png"],
            "MEYSA": ["img/t-shirt_details/meysa_a.webp", "img/t-shirt_details/meysa_b.webp", "img/t-shirt_details/meysa_c.webp"],
            "INFERNO": ["img/t-shirt_details/inferno_a.jpg", "img/t-shirt_details/inferno_b.jpg", "img/t-shirt_details/inferno_c.png"]
        };
        
        const sizeImg = "img/t-shirt_details/size.webp";
        const small = document.getElementsByClassName('smol-img');

        Object.keys(productImages).forEach(key => {
            if (savedTitle.includes(key)) {
                small[0].src = productImages[key][0];
                small[1].src = productImages[key][1];
                small[2].src = sizeImg;
                small[3].src = productImages[key][2];
                
                descElement.innerText = "Guaranteed to make a bold statement on the road! Air Cool Fabric Riding Gear is a game-changer for riders seeking the perfect balance of comfort, style, and performance—crafted with advanced breathability and moisture-wicking properties, it keeps you cool and dry throughout your journey, while ensuring a snug and flexible fit for unrestricted movement; designed to cater to riders of all genders. Crafted with Precision, Worn with Pride—a Philippine-Made Product.";
                titleElement1.innerText = `T-SHIRT - "${key}" Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING`;
            }
        });
        
        // Click event for small images
        Array.from(small).forEach(img => {
            img.addEventListener("click", () => mainImg.src = img.src);
        });

        // Price selector logic
        document.getElementById("sizeSelector").addEventListener("change", function () {
            let selectedPrice = this.value;
            priceElement.textContent = selectedPrice ? `₱${selectedPrice}` : "₱399-599";
        });
    });
}
