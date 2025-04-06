<?php
// This file should be included at the very top of all admin pages, right after <head>
?>
<!-- Theme initializer - must be in the head -->
<script>
  (function() {
    // Immediately set theme before any rendering happens
    const theme = localStorage.getItem('theme') || 'light';
    document.documentElement.className = 'theme-preload theme-' + theme;
    
    // Create and apply inline critical CSS
    const style = document.createElement('style');
    style.textContent = `
      :root {
        color-scheme: ${theme === 'dark' ? 'dark' : 'light'};
      }
      body {
        background-color: ${theme === 'dark' ? '#1a1f2b' : '#ffffff'} !important;
        color: ${theme === 'dark' ? '#e1e1e1' : '#212529'} !important;
        visibility: visible !important;
      }
      .theme-light-logo { display: ${theme === 'light' ? 'inline-block' : 'none'} !important; }
      .theme-dark-logo { display: ${theme === 'dark' ? 'inline-block' : 'none'} !important; }
    `;
    document.head.appendChild(style);
  })();
</script>