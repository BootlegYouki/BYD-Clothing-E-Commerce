// Automatically remove .php extension from all href attributes, but only when not on localhost
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on localhost
    const isLocalhost = window.location.hostname === 'localhost' || 
                        window.location.hostname === '127.0.0.1' ||
                        window.location.hostname.startsWith('192.168.') ||
                        window.location.hostname.includes('.local');
                        
    // Only run the URL cleaner if we're NOT on localhost
    if (!isLocalhost) {
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
    }
  });