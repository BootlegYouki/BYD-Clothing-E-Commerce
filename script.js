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