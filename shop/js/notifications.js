/**
 * BYD Clothing - Notification System
 * Handles notification interactions such as marking as read
 */

document.addEventListener('DOMContentLoaded', function() {
    initNotifications();
});

function initNotifications() {
    // Get all mark read buttons
    const markReadButtons = document.querySelectorAll('.mark-read-btn');
    
    // Add event listeners to each button
    markReadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            markNotificationAsRead(this);
        });
    });
}

/**
 * Mark a notification as read and remove it from the list
 * @param {HTMLElement} button - The button that was clicked
 */
function markNotificationAsRead(button) {
    // Get the notification item
    const notificationItem = button.closest('.notification-item');
    
    // Add a visual effect before removing
    notificationItem.style.transition = 'opacity 0.3s ease-out';
    notificationItem.style.opacity = '0';
    
    // Wait for animation to complete then remove
    setTimeout(() => {
        notificationItem.remove();
        
        // Update notification badge count
        updateNotificationCount();
        
        // Check if there are any notifications left
        checkEmptyNotifications();
    }, 300);
    
    // Here you would typically send an AJAX request to mark the notification as read in the database
    // Example:
    // const notificationId = button.dataset.notificationId;
    // fetch('api/notifications/mark-read.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //     },
    //     body: JSON.stringify({ id: notificationId }),
    // });
}

/**
 * Update the notification badge count
 */
function updateNotificationCount() {
    const badge = document.querySelector('.notification-badge');
    const visibleNotifications = document.querySelectorAll('.notification-item:not(.d-none)').length;
    
    // Update the badge count
    badge.textContent = visibleNotifications;
    
    // Hide badge if no notifications
    if (visibleNotifications === 0) {
        badge.style.display = 'none';
    }
}

/**
 * Check if there are any notifications and show/hide empty state
 */
function checkEmptyNotifications() {
    const notificationBody = document.querySelector('.notification-body');
    const emptyNotification = document.querySelector('.empty-notification');
    const visibleNotifications = document.querySelectorAll('.notification-item:not(.d-none)').length;
    
    if (visibleNotifications === 0) {
        // Show empty notification message
        emptyNotification.classList.remove('d-none');
    } else {
        // Hide empty notification message
        emptyNotification.classList.add('d-none');
    }
}

/**
 * Fetch notifications from the server (placeholder for future implementation)
 */
function fetchNotifications() {
    // This function would be used to get notifications from the server
    // Example implementation:
    // fetch('api/notifications/get.php')
    //     .then(response => response.json())
    //     .then(data => {
    //         renderNotifications(data);
    //     })
    //     .catch(error => {
    //         console.error('Error fetching notifications:', error);
    //     });
}
