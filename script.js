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

$(document).ready(function(){
  $('.navbar-toggler').on('click', function(){
      var $icon = $('#bar');
      $icon.text($icon.text().trim() === "menu" ? "close" : "menu");
  });
});