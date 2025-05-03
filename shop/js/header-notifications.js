/**
 * Header Notifications System for BYD Clothing Shop
 * Handles notification dropdown, loading, marking as read, and counter updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize header notifications
    initHeaderNotifications();
    
    // Check for unread notifications on page load
    checkUnreadNotifications();
    
    // Listen for notification read events from the notifications page
    document.addEventListener('notificationRead', function(e) {
        if (e.detail && typeof e.detail.unreadCount !== 'undefined') {
            // Update the badge count directly from the event data
            updateNotificationBadge(e.detail.unreadCount);
        }
    });
    
    // Listen for user logged in event
    document.addEventListener('userLoggedIn', function(e) {
        if (e.detail && !e.detail.isAdmin) {
            // Short delay to ensure DOM is updated first
            setTimeout(() => {
                // Re-initialize notifications system
                initHeaderNotifications();
                checkUnreadNotifications();
            }, 300);
        }
    });
});

/**
 * Initialize notifications system for header dropdown
 */
function initHeaderNotifications() {
    // Get notification dropdown element
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (!notificationDropdown) return;

    // Load notifications when dropdown is opened
    // Remove any existing event listeners first
    const newDropdown = notificationDropdown.cloneNode(true);
    if (notificationDropdown.parentNode) {
        notificationDropdown.parentNode.replaceChild(newDropdown, notificationDropdown);
    }

    // Add new event listener
    newDropdown.addEventListener('click', function(e) {
        loadHeaderNotifications();
    });
}

/**
 * Load notifications into the dropdown
 */
function loadHeaderNotifications() {
    const notificationList = document.querySelector('.notification-list');
    const emptyNotification = document.querySelector('.empty-notification');
    const loadingNotifications = document.querySelector('.loading-notifications');
    
    // Show loading, hide empty state
    if (loadingNotifications) loadingNotifications.classList.remove('d-none');
    if (emptyNotification) emptyNotification.classList.add('d-none');
    if (notificationList) notificationList.innerHTML = '';

    // Fetch notifications from the server
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
                notificationList.innerHTML = ''; // Clear the list first to prevent duplicates
                data.notifications.forEach(notification => {
                    notificationList.innerHTML += createNotificationItem(notification);
                });

                // Add event listeners to mark as read buttons
                addMarkAsReadListeners();
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
 * Add event listeners to mark as read buttons
 */
function addMarkAsReadListeners() {
    document.querySelectorAll('.notification-list .mark-read-btn').forEach(btn => {
        // Remove any existing event listeners first
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = this.getAttribute('data-notification-id');
            markNotificationAsRead(notificationId, this);
            return false; // Prevent any further propagation
        });
    });
}

/**
 * Check for unread notifications and update badge
 */
function checkUnreadNotifications() {
    fetch('functions/notification/get-notifications.php?count_only=1')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_count);
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
 * Mark a notification as read
 * @param {string} notificationId - The notification ID
 * @param {HTMLElement} button - The button element clicked
 */
function markNotificationAsRead(notificationId, button) {
    // Get the notification item container
    const notificationItem = button.closest('.notification-item');
    
    // Prevent multiple clicks
    if (button.disabled) return;
    button.disabled = true;
    
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
            // IMPORTANT: Dispatch event BEFORE modifying the DOM
            // This ensures the notifications page can find and update the element
            // before it gets hidden or removed from the header
            const event = new CustomEvent('headerNotificationRead', {
                detail: {
                    notificationId: notificationId,
                    unreadCount: data.unread_count
                }
            });
            document.dispatchEvent(event);
            
            // Add a small delay before removing notification from header
            setTimeout(() => {
                // Apply visual changes to header notification
                if (notificationItem) {
                    notificationItem.style.transition = 'all 0.3s ease';
                    notificationItem.style.opacity = '0';
                    notificationItem.style.maxHeight = '0';
                    notificationItem.style.padding = '0';
                    notificationItem.style.margin = '0';
                    notificationItem.style.border = 'none';
                    notificationItem.style.overflow = 'hidden';
                    
                    setTimeout(() => {
                        notificationItem.remove();
                        
                        // Check if there are any notifications left
                        const notificationList = document.querySelector('.notification-list');
                        const emptyNotification = document.querySelector('.empty-notification');
                        
                        if (notificationList && notificationList.children.length === 0 && emptyNotification) {
                            emptyNotification.classList.remove('d-none');
                        }
                    }, 300);
                }
                
                // Update badge count
                updateNotificationBadge(data.unread_count);
            }, 500); // Longer delay to ensure page has time to process
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
 * Create HTML for a notification item
 * @param {Object} notification - Notification data
 * @returns {string} HTML string for notification item
 */
function createNotificationItem(notification) {
    const timeAgo = notification.time_ago || 'Just now';
    
    return `
        <div class="notification-item p-3 border-bottom" data-id="${notification.id}">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="${notification.icon || 'bx bx-bell'} fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${notification.title}</h6>
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
