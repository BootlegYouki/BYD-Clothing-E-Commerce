/**
 * Notifications Page System for BYD Clothing Shop
 * Handles page-specific notification functions like filters and mark all as read
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification filters
    initNotificationFilters();
    
    // Initialize mark all as read functionality
    initMarkAllAsRead();
    
    // Add event listeners for mark as read buttons on the page
    addPageMarkAsReadListeners();
    
    // Listen for notification read events from the header dropdown
    document.addEventListener('headerNotificationRead', function(e) {
        if (e.detail && e.detail.notificationId) {
            // Find and update the notification on the page
            syncNotificationFromHeader(e.detail.notificationId, e.detail.unreadCount);
        }
    });
    
    // Check if "Mark All as Read" button should be disabled (if all notifications are already read)
    updateMarkAllButtonState();
});

/**
 * Add event listeners to mark as read buttons on notifications page
 */
function addPageMarkAsReadListeners() {
    document.querySelectorAll('.notification-group .mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = this.getAttribute('data-notification-id');
            markPageNotificationAsRead(notificationId, this);
        });
    });
}

/**
 * Mark a notification as read on the notifications page
 * @param {string} notificationId - The notification ID
 * @param {HTMLElement} button - The button element clicked
 */
function markPageNotificationAsRead(notificationId, button) {
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
            // Update the notification item instead of removing it
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                
                // Update styling
                const title = notificationItem.querySelector('h6');
                if (title) {
                    title.classList.remove('fw-bold');
                    title.classList.add('text-muted');
                }
                
                // Remove badges
                const badges = notificationItem.querySelectorAll('.notification-badge-page');
                badges.forEach(badge => badge.remove());
                
                // Remove mark as read button
                const markReadContainer = button.closest('.mt-2');
                if (markReadContainer) {
                    markReadContainer.remove();
                }
                
                // Restore opacity
                notificationItem.style.opacity = '1';
            }
            
            // Update counter if present
            updatePageNotificationCounter(data.unread_count);
            
            // Dispatch a custom event to update the header notification badge
            const event = new CustomEvent('notificationRead', { 
                detail: { unreadCount: data.unread_count } 
            });
            document.dispatchEvent(event);
        } else {
            // Restore opacity if error
            if (notificationItem) {
                notificationItem.style.opacity = '1';
            }
            button.disabled = false;
            showToast(data.message || 'Error marking notification as read', 'danger');
            console.error('Error marking notification as read:', data.message);
        }
    })
    .catch(error => {
        // Restore opacity if error
        if (notificationItem) {
            notificationItem.style.opacity = '1';
        }
        button.disabled = false;
        showToast('Error marking notification as read', 'danger');
        console.error('Error marking notification as read:', error);
    });
}

/**
 * Sync a notification marked as read from the header dropdown
 * @param {string} notificationId - The notification ID that was marked as read
 * @param {number} unreadCount - Updated unread count
 */
function syncNotificationFromHeader(notificationId, unreadCount) {
    // Find the notification item on the page
    const notificationItems = document.querySelectorAll(`.notification-item[data-id="${notificationId}"]`);
    
    if (notificationItems.length === 0) {
        console.log('Notification not found on page, cannot sync from header');
        // Still update the counter even if we can't find the notification
        updatePageNotificationCounter(unreadCount);
        return;
    }
    
    // Update all instances of this notification on the page
    notificationItems.forEach(item => {
        // Force remove all styles that might be applied from the header script
        item.removeAttribute('style');
        
        // Mark as read
        item.classList.remove('unread');
        
        // Update title styling
        const title = item.querySelector('h6');
        if (title) {
            title.classList.remove('fw-bold');
            title.classList.add('text-muted');
        }
        
        // Update message styling
        const message = item.querySelector('p.mb-1');
        if (message) {
            message.classList.add('text-secondary');
        }
        
        // Remove badges
        const badges = item.querySelectorAll('.notification-badge-page');
        badges.forEach(badge => badge.remove());
        
        // Remove mark as read button
        const markReadContainer = item.querySelector('.mt-2');
        if (markReadContainer) {
            markReadContainer.remove();
        }
    });
    
    // Update counter
    updatePageNotificationCounter(unreadCount);
    
    // Also update the "Mark All as Read" button state
    updateMarkAllButtonState(unreadCount);
}

/**
 * Update notification counter on the page
 * @param {number} count - Number of unread notifications
 */
function updatePageNotificationCounter(count) {
    const counter = document.querySelector('.notification-counter');
    if (!counter) return;
    
    if (count > 0) {
        counter.textContent = count + ' New';
    } else {
        counter.remove();
    }
    
    // Update the "Mark All as Read" button state
    updateMarkAllButtonState(count);
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
                
                // Dispatch a custom event to update the header notification badge
                const event = new CustomEvent('notificationRead', { 
                    detail: { unreadCount: 0 } 
                });
                document.dispatchEvent(event);
                
                // Keep the button disabled since all notifications are now read
                this.disabled = true;
            } else {
                showToast(data.message || 'Error marking all as read', 'danger');
                // Restore button state
                this.innerHTML = originalHtml;
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            showToast('Error marking all as read', 'danger');
            // Restore button state
            this.innerHTML = originalHtml;
            this.disabled = false;
        });
    });
}

/**
 * Update the state of the "Mark All as Read" button based on unread count
 * @param {number|null} count - Optional unread count, if not provided it will be calculated
 */
function updateMarkAllButtonState(count = null) {
    const markAllButton = document.querySelector('button[name="mark_all_read"]');
    if (!markAllButton) return;
    
    if (count !== null) {
        // If count is explicitly provided, use it
        markAllButton.disabled = count <= 0;
        return;
    }
    
    // Otherwise count unread notifications in the DOM
    const unreadItems = document.querySelectorAll('.notification-item.unread').length;
    markAllButton.disabled = unreadItems <= 0;
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
