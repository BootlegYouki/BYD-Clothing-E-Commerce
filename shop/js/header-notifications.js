/**
 * Header Notifications System for BYD Clothing Shop
 * Handles notification loading, marking as read, and counter updates in the header
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize header notifications
    initHeaderNotifications();
    
    // Listen for notification updates from other parts of the app
    document.addEventListener('notificationMarkedAsRead', function(e) {
        // Update badge count
        if (e.detail && typeof e.detail.unreadCount !== 'undefined') {
            updateNotificationBadge(e.detail.unreadCount);
        } else {
            // If count not provided, fetch current count
            checkUnreadNotifications();
        }
        
        // If notification dropdown is open, refresh its content
        if (document.querySelector('.notification-dropdown.show')) {
            loadHeaderNotifications();
        }
    });
    
    // Listen for all notifications marked as read
    document.addEventListener('allNotificationsMarkedAsRead', function() {
        // Update badge to zero
        updateNotificationBadge(0);
        
        // If notification dropdown is open, refresh to show empty state
        if (document.querySelector('.notification-dropdown.show')) {
            loadHeaderNotifications();
        }
    });
});

/**
 * Initialize header notifications system
 */
function initHeaderNotifications() {
    // Get notification dropdown element
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (!notificationDropdown) return;

    // Load notifications when dropdown is opened
    notificationDropdown.addEventListener('click', function() {
        loadHeaderNotifications();
    });

    // Check for unread notifications on page load
    checkUnreadNotifications();
    
    // Setup automatic polling every 5 seconds
    setInterval(checkUnreadNotifications, 5000);
}

/**
 * Load notifications into the header dropdown
 */
function loadHeaderNotifications() {
    const notificationList = document.querySelector('.notification-list');
    const emptyNotification = document.querySelector('.empty-notification');
    const loadingNotifications = document.querySelector('.loading-notifications');
    
    // Show loading, hide empty state
    if (loadingNotifications) loadingNotifications.classList.remove('d-none');
    if (emptyNotification) emptyNotification.classList.add('d-none');
    if (notificationList) notificationList.innerHTML = '';

    // Fetch notifications from the server - only get unread notifications for the header
    fetch('functions/notification/get-notifications.php?limit=5&unread_only=1')
        .then(response => response.json())
        .then(data => {
            // Hide loading indicator
            if (loadingNotifications) loadingNotifications.classList.add('d-none');
            
            if (!data.notifications || data.notifications.length === 0) {
                // Show empty state if no notifications
                if (emptyNotification) emptyNotification.classList.remove('d-none');
                return;
            }

            // Render notifications
            if (notificationList) {
                notificationList.innerHTML = ''; // Clear the list first
                data.notifications.forEach(notification => {
                    notificationList.innerHTML += createHeaderNotificationItem(notification);
                });

                // Add event listeners to "mark as read" buttons
                document.querySelectorAll('.notification-list .mark-read-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const notificationId = this.getAttribute('data-notification-id');
                        markNotificationAsReadFromHeader(notificationId, this);
                    });
                });
            }

            // Update the notification badge
            updateNotificationBadge(data.unread_count);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            // Hide loading, show error
            if (loadingNotifications) loadingNotifications.classList.add('d-none');
            if (notificationList) {
                notificationList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bx bx-error-circle fs-1 text-danger"></i>
                        <p class="text-muted mt-2">Error loading notifications</p>
                    </div>
                `;
            }
        });
}

/**
 * Check for unread notifications and update badge
 */
function checkUnreadNotifications() {
    fetch('functions/notification/get-notifications.php?count_only=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
            }
        })
        .catch(error => {
            console.error('Error checking unread notifications:', error);
        });
}

/**
 * Update the notification badge count
 * @param {number} count - Number of unread notifications
 */
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (!badge) return;

    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Mark a notification as read from the header dropdown
 * @param {string} notificationId - The notification ID
 * @param {HTMLElement} button - The button element clicked
 */
function markNotificationAsReadFromHeader(notificationId, button) {
    // Get the notification item container
    const notificationItem = button.closest('.notification-item');
    
    // Prevent multiple clicks
    if (button.disabled) return;
    button.disabled = true;
    
    // Add visual feedback immediately
    if (notificationItem) {
        notificationItem.style.opacity = '0.5';
    }
    
    // Make AJAX request to mark notification as read
    fetch('functions/notification/mark-notification-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification item with animation
            if (notificationItem) {
                notificationItem.style.transition = 'all 0.3s ease';
                notificationItem.style.opacity = '0';
                notificationItem.style.maxHeight = '0';
                notificationItem.style.padding = '0';
                notificationItem.style.margin = '0';
                notificationItem.style.border = 'none';
                setTimeout(() => {
                    notificationItem.remove();
                    
                    // Check if there are any notifications left
                    const notificationList = document.querySelector('.notification-list');
                    const emptyNotification = document.querySelector('.empty-notification');
                    const loadingNotifications = document.querySelector('.loading-notifications');
                    
                    if (notificationList && notificationList.children.length === 0) {
                        // No notifications left - show empty state and hide loading
                        if (emptyNotification) emptyNotification.classList.remove('d-none');
                        if (loadingNotifications) loadingNotifications.classList.add('d-none');
                    }
                }, 300);
            }
            
            // Update badge count
            updateNotificationBadge(data.unread_count);
            
            // Dispatch event for other parts of the app
            document.dispatchEvent(new CustomEvent('notificationMarkedAsRead', {
                detail: {
                    notificationId: notificationId,
                    unreadCount: data.unread_count
                }
            }));
        } else {
            // Restore opacity if error
            if (notificationItem) {
                notificationItem.style.opacity = '1';
            }
            button.disabled = false;
            console.error('Error marking notification as read:', data.message);
        }
    })
    .catch(error => {
        // Restore opacity if error
        if (notificationItem) {
            notificationItem.style.opacity = '1';
        }
        button.disabled = false;
        console.error('Error marking notification as read:', error);
    });
}

/**
 * Create HTML for a notification item in header dropdown
 * @param {Object} notification - Notification data
 * @returns {string} HTML string for notification item
 */
function createHeaderNotificationItem(notification) {
    const timeAgo = notification.time_ago || 'Just now';
    
    return `
        <div class="notification-item p-3 border-bottom" data-id="${notification.id}">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="${notification.icon || 'bx bx-bell'} fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1 fw-bold">${notification.title}</h6>
                    <p class="text-muted small mb-1">${notification.message}</p>
                    <small class="text-muted">${timeAgo}</small>
                </div>
                <div class="align-self-center ms-2">
                    <button class="btn btn-sm btn-light rounded-circle mark-read-btn" 
                            title="Mark as read" 
                            data-notification-id="${notification.id}">
                        <i class="bx bx-check"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}
