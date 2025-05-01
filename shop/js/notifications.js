/**
 * Notifications System for BYD Clothing Shop
 * Handles notification loading, marking as read, and counter updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notifications
    initNotifications();

    // Check if we're on the notifications page
    const isNotificationsPage = window.location.pathname.includes('notifications.php');
    if (isNotificationsPage) {
        // Initialize additional functions for the notifications page
        initNotificationFilters();
        initMarkAllAsRead();
    }
});

/**
 * Initialize notifications system
 */
function initNotifications() {
    // Get notification dropdown element
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (!notificationDropdown) return;

    // Load notifications when dropdown is opened
    notificationDropdown.addEventListener('click', function(e) {
        loadNotifications();
    });

    // Check for unread notifications on page load
    checkUnreadNotifications();
}

/**
 * Load notifications into the dropdown
 */
function loadNotifications() {
    const notificationList = document.querySelector('.notification-list');
    const emptyNotification = document.querySelector('.empty-notification');
    const loadingNotifications = document.querySelector('.loading-notifications');
    
    // Show loading, hide empty state
    if (loadingNotifications) loadingNotifications.classList.remove('d-none');
    if (emptyNotification) emptyNotification.classList.add('d-none');
    if (notificationList) notificationList.innerHTML = '';

    // Fetch notifications from the server
    fetch('functions/notification/get-notifications.php?limit=5&unread_only=1') // Only load unread notifications
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

                // Remove any existing event listeners before adding new ones
                document.querySelectorAll('.mark-read-btn').forEach(btn => {
                    // Clone and replace the button to remove all event listeners
                    const newBtn = btn.cloneNode(true);
                    btn.parentNode.replaceChild(newBtn, btn);
                    
                    // Add fresh event listener
                    newBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const notificationId = this.getAttribute('data-notification-id');
                        markNotificationAsRead(notificationId, this);
                        return false; // Prevent any further propagation
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

/**
 * Adjust time display to handle timezone issues
 * @param {string} timeAgo - Server-calculated time ago string
 * @param {string} createdAt - Raw timestamp from server
 * @returns {string} Corrected time ago
 */
function adjustTimeDisplay(timeAgo, createdAt) {
    if (!createdAt) return timeAgo;
    
    // Convert server timestamp to client's local time
    // Assuming server uses Philippines time (UTC+8)
    const serverTime = new Date(createdAt);
    const now = new Date();
    
    // Add UTC+8 offset to match Philippines time
    const serverTimezoneOffset = 8 * 60 * 60 * 1000; // 8 hours in milliseconds
    const clientOffset = now.getTimezoneOffset() * 60 * 1000; // Client offset in milliseconds
    const adjustedServerTime = new Date(serverTime.getTime() + clientOffset + serverTimezoneOffset);
    
    // Calculate time difference in seconds
    const diffSeconds = Math.floor((now - adjustedServerTime) / 1000);
    
    // Return appropriate time ago message
    if (diffSeconds < 60) {
        return "Just now";
    } else if (diffSeconds < 3600) {
        const mins = Math.floor(diffSeconds / 60);
        return `${mins} minute${mins > 1 ? 's' : ''} ago`;
    } else if (diffSeconds < 86400) {
        const hours = Math.floor(diffSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (diffSeconds < 604800) {
        const days = Math.floor(diffSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else if (diffSeconds < 2592000) {
        const weeks = Math.floor(diffSeconds / 604800);
        return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
    } else if (diffSeconds < 31536000) {
        const months = Math.floor(diffSeconds / 2592000);
        return `${months} month${months > 1 ? 's' : ''} ago`;
    } else {
        const years = Math.floor(diffSeconds / 31536000);
        return `${years} year${years > 1 ? 's' : ''} ago`;
    }
}

/**
 * Initialize notification filters on the notifications page
 */
function initNotificationFilters() {
    // Filter functionality for notification types
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length === 0) return;
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter notifications
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-type') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide date headers based on visible items
            updateDateHeaderVisibility();
        });
    });
}

/**
 * Update date header visibility based on visible notification items
 */
function updateDateHeaderVisibility() {
    const groups = document.querySelectorAll('.notification-group');
    groups.forEach(group => {
        const visibleItems = group.querySelectorAll('.notification-item:not([style*="display: none"])').length;
        const header = group.previousElementSibling;
        
        if (header && header.classList.contains('notification-date-header')) {
            header.style.display = visibleItems > 0 ? 'block' : 'none';
        }
    });
}

/**
 * Initialize mark all as read functionality
 */
function initMarkAllAsRead() {
    const markAllButton = document.querySelector('button[name="mark_all_read"]');
    if (!markAllButton) return;
    
    markAllButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Add loading state
        const originalHtml = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        this.disabled = true;
        
        // Make AJAX request to mark all as read
        fetch('functions/notification/mark-all-read.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI to mark all as read
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    
                    // Update styling
                    const title = item.querySelector('h6');
                    if (title) {
                        title.classList.remove('fw-bold');
                        title.classList.add('text-muted');
                    }
                    
                    // Remove badges
                    const badges = item.querySelectorAll('.notification-badge-page');
                    badges.forEach(badge => badge.remove());
                    
                    // Remove mark as read buttons
                    const markReadContainers = item.querySelectorAll('.mt-2');
                    markReadContainers.forEach(container => container.remove());
                });
                
                // Remove notification counter
                const counter = document.querySelector('.notification-counter');
                if (counter) counter.remove();
                
                // Show success message
                showToast('All notifications marked as read', 'success');
            } else {
                showToast(data.message || 'Error marking all as read', 'danger');
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            showToast('Error marking all as read', 'danger');
        })
        .finally(() => {
            // Restore button state
            this.innerHTML = originalHtml;
            this.disabled = false;
        });
    });
}

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Bootstrap alert type (success, danger, etc.)
 */
function showToast(message, type) {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1060';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = 'toast';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="toast-header ${type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'}">
            <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Show toast using Bootstrap
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}
