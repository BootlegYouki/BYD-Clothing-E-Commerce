<footer class="footer pt-5">
</footer>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../assets/js/perfect-scrollbar.min.js"></script>
<script src="../assets/js/smooth-scrollbar.min.js"></script>
<script src="../assets/js/file-upload.js"></script>
<!-- <script>
// Automatically remove .php extension from all href attributes
document.addEventListener('DOMContentLoaded', function() {
  // Find all anchor tags with href attributes containing .php
  const links = document.querySelectorAll('a[href*=".php"]');
  
  // Convert NodeList to Array and process each link
  Array.from(links).forEach(link => {
    const href = link.getAttribute('href');
    
    // If href contains .php but doesn't have query parameters
    if (href.includes('.php') && !href.includes('.php?')) {
      // Replace .php with nothing
      const newHref = href.replace('.php', '');
      link.setAttribute('href', newHref);
    } 
    // If href contains .php followed by query parameters
    else if (href.includes('.php?')) {
      // Replace .php with just ? to preserve query parameters
      const newHref = href.replace('.php?', '?');
      link.setAttribute('href', newHref);
    }
  });
  
  // Also process onclick attributes that redirect to php pages
  const elementsWithOnclick = document.querySelectorAll('[onclick*=".php"]');
  Array.from(elementsWithOnclick).forEach(element => {
    const onclickAttr = element.getAttribute('onclick');
    if (onclickAttr) {
      // Replace .php with empty string in onclick handlers
      const newOnclick = onclickAttr.replace(/\.php(['")\s])/g, '$1');
      element.setAttribute('onclick', newOnclick);
    }
  });
  
  // Process form actions too
  const forms = document.querySelectorAll('form[action*=".php"]');
  Array.from(forms).forEach(form => {
    const action = form.getAttribute('action');
    if (action.includes('.php')) {
      const newAction = action.replace('.php', '');
      form.setAttribute('action', newAction);
    }
  });
});
</script> -->
</body>
</html>