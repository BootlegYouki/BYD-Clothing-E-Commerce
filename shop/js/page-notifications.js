/**
 * Page Notifications System for BYD Clothing Shop
 * Handles notification management on the notifications page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notifications page functionality
    initPageNotifications();
    
    // Listen for notifications marked as read from header
    document.addEventListener('notificationMarkedAsRead', function(e) {
        if (e.detail && e.detail.notificationId) {
            // Update the UI for this notification on the page
            updateNotificationUIAfterRead(e.detail.notificationId);
        }
    });
});

/**
 * Initialize notifications page functionality
 */
function initPageNotifications() {
    // Initialize notification filters
    initNotificationFilters();
    
    // Initialize mark all as read functionality
    initMarkAllAsRead();
    
    // Add event listeners to individual "mark as read" buttons
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-notification-id');
            markNotificationAsReadFromPage(notificationId, this);
        });
    });
    
    // Setup automatic polling every 5 seconds
    setInterval(checkPageNotifications, 5000);
}

/**
 * Check for new notifications and update the page
 */
function checkPageNotifications() {
    // Fetch notifications from the server
    fetch('functions/notification/get-notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification counter
                updatePageNotificationCounter(data.unread_count);
                
                // Check if we need to update the notifications list
                updatePageNotificationsIfNeeded(data.notifications);
                
                // Enable/disable mark all as read button based on unread count
                const markAllButton = document.querySelector('button[name="mark_all_read"]');
                if (markAllButton) {
                    markAllButton.disabled = data.unread_count === 0;
                }
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
}

/**
 * Update page notifications if there are changes
 * @param {Array} notifications - Latest notifications from server
 */
function updatePageNotificationsIfNeeded(notifications) {
    if (!notifications || !notifications.length) return;
    
    // Check if any new notifications need to be added
    const currentIds = Array.from(document.querySelectorAll('.notification-item'))
        .map(item => item.getAttribute('data-id'));
    
    // Find notifications that aren't already on the page
    const newNotifications = notifications.filter(notification => 
        !currentIds.includes(notification.id.toString()));
    
    if (newNotifications.length > 0) {
        // Refresh the page to show new notifications
        // This is a simple approach; for a more sophisticated implementation,
        // you could dynamically insert the new notifications
        location.reload();
    }
}

/**
 * Mark a notification as read from the notifications page
 * @param {string} notificationId - The notification ID
 * @param {HTMLElement} button - The button element clicked
 */
function markNotificationAsReadFromPage(notificationId, button) {
    // Get the notification item container
    const notificationItem = button.closest('.notification-item');
    
    // Prevent multiple clicks
    if (button.disabled) return;
    button.disabled = true;
    
    // Add visual feedback immediately
    if (notificationItem) {
        notificationItem.style.opacity = '0.7';
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
            // Update UI to show notification as read
            if (notificationItem) {
                // Remove unread class
                notificationItem.classList.remove('unread');
                
                // Update the title style
                const title = notificationItem.querySelector('h6');
                if (title) {
                    title.classList.remove('fw-bold');
                    title.classList.add('text-muted');
                }
                
                // Remove the notification badge indicator
                const badge = notificationItem.querySelector('.notification-badge-page');
                if (badge) {
                    badge.remove();
                }
                
                // Hide the mark as read button container
                const btnContainer = button.closest('.mt-2');
                if (btnContainer) {
                    btnContainer.style.display = 'none';
                }
                
                // Restore full opacity
                notificationItem.style.opacity = '1';
            }
            
            // Update notification counter in page header if exists
            updatePageNotificationCounter(data.unread_count);
            
            // Dispatch event for other parts of the app (like the header)
            document.dispatchEvent(new CustomEvent('notificationMarkedAsRead', {
                detail: {
                    notificationId: notificationId,
                    unreadCount: data.unread_count
                }
            }));
            
            // Show success message
            showToast('Notification marked as read', 'success');
        } else {
            // Restore opacity if error
            if (notificationItem) {
                notificationItem.style.opacity = '1';
            }
            button.disabled = false;
            console.error('Error marking notification as read:', data.message);
            showToast(data.message || 'Error marking notification as read', 'danger');
        }
    })
    .catch(error => {
        // Restore opacity if error
        if (notificationItem) {
            notificationItem.style.opacity = '1';
        }
        button.disabled = false;
        console.error('Error marking notification as read:', error);
        showToast('Error marking notification as read', 'danger');
    });
}

/**
 * Update UI after a notification is marked as read (for sync from header)
 * @param {string} notificationId - ID of the notification that was marked as read
 */
function updateNotificationUIAfterRead(notificationId) {
    // Find the notification item on the page
    const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
    if (!notificationItem) return;
    
    // Remove unread class
    notificationItem.classList.remove('unread');
    
    // Update the title style
    const title = notificationItem.querySelector('h6');
    if (title) {
        title.classList.remove('fw-bold');
        title.classList.add('text-muted');
    }
    
    // Remove the notification badge indicator
    const badge = notificationItem.querySelector('.notification-badge-page');
    if (badge) {
        badge.remove();
    }
    
    // Hide the mark as read button container
    const btnContainer = notificationItem.querySelector('.mt-2');
    if (btnContainer) {
        btnContainer.style.display = 'none';
    }
    
    // Get the current unread count
    fetch('functions/notification/get-notifications.php?count_only=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update counter in page header
                updatePageNotificationCounter(data.unread_count);
            }
        })
        .catch(error => {
            console.error('Error getting unread count:', error);
        });
}

/**
 * Initialize notification filters
 */
function initNotificationFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (!filterButtons.length) return;
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button state
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
            
            // Update date header visibility
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
    
    // Initial button state
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    markAllButton.disabled = unreadItems.length === 0;
    
    markAllButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Check if there are any unread notifications
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        if (unreadItems.length === 0) {
            return; // Don't proceed if nothing to mark as read
        }
        
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
                    
                    // Update styling for title
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
                
                // Remove notification counter in page header
                updatePageNotificationCounter(0);
                
                // Dispatch event for other parts of the app (like the header)
                document.dispatchEvent(new CustomEvent('allNotificationsMarkedAsRead'));
                
                // Only show success message if there were actually unread notifications
                if (unreadItems.length > 0) {
                    showToast('All notifications marked as read', 'success');
                }
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
            this.disabled = true; // Keep disabled as there are no more unread notifications
        });
    });
}

/**
 * Update notification counter in page header
 * @param {number} count - Unread notification count
 */
function updatePageNotificationCounter(count) {
    // First try to update the counter
    let counter = document.querySelector('.notification-counter');
    
    if (count > 0) {
        if (counter) {
            // Update existing counter
            counter.textContent = `${count} New`;
        } else {
            // Create new counter if it doesn't exist
            const pageHeader = document.querySelector('h2.fw-bold');
            if (pageHeader) {
                counter = document.createElement('span');
                counter.className = 'notification-counter';
                counter.textContent = `${count} New`;
                pageHeader.appendChild(counter);
            }
        }
    } else if (counter) {
        // Remove counter if count is 0
        counter.remove();
    }
}

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Bootstrap alert type (success, danger, etc.)
 */
function showToast(message, type) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast' + Date.now();
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    
    toast.innerHTML = `
        <div class="toast-header ${bgClass} text-white">
            <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show the toast
    const bsToast = new bootstrap.Toast(toast, {
        delay: 3000
    });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
