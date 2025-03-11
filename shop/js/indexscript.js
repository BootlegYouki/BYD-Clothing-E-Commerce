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

function copyPhoneNumber() {
  const phone = "0905 507 9634";
  navigator.clipboard.writeText(phone)
      .then(() => {
          alert('Phone number copied to clipboard');
      })
      .catch(err => {
          alert('Failed to copy text: ' + err);
      });
}

const header = document.querySelector("header");
window.addEventListener("scroll", function(){
  header.classList.toggle("sticky", this.window.scrollY > 0);
})

let menu = document.querySelector('#menu-icon');
let navmenu = document.querySelector('.navmenu');

menu.onclick = () => {
  menu.classList.toggle('bx-x');
  navmenu.classList.toggle('open');
}