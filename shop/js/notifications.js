/**
 * BYD Clothing - Notification System
 * Handles notification interactions such as marking as read
 */

document.addEventListener('DOMContentLoaded', function() {
    initNotifications();
    
    // Check if we're on the full notifications page
    const isNotificationsPage = window.location.pathname.includes('notifications.php');
    
    if (isNotificationsPage) {
        initNotificationFilters();
        initMarkAllAsRead();
    }
});

function initNotifications() {
    // Get all mark read buttons
    const markReadButtons = document.querySelectorAll('.mark-read-btn');
    
    // Add event listeners to each button
    markReadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if we're in the dropdown or full page context
            const isDropdown = this.closest('.notification-dropdown') !== null;
            const notificationItem = this.closest('.notification-item');
            const notificationId = this.closest('form')?.querySelector('input[name="notification_id"]')?.value || 
                                   this.dataset.notificationId;
            
            if (isDropdown) {
                // Dropdown behavior
                markNotificationAsReadInDropdown(this);
            } else {
                // Full page behavior - use existing AJAX
                markNotificationAsReadOnPage(notificationItem, notificationId);
            }
        });
    });
}

/**
 * Mark a notification as read in the dropdown
 * @param {HTMLElement} button - The button that was clicked
 */
function markNotificationAsReadInDropdown(button) {
    // Get the notification item
    const notificationItem = button.closest('.notification-item');
    
    // Add a visual effect before removing - THIS WAS CAUSING DELAY
    // Update to give immediate feedback instead of waiting
    notificationItem.style.opacity = '0.5'; // Immediate feedback
    
    // Update notification badge count immediately
    updateNotificationCount();
    
    // Here you would typically send an AJAX request to mark the notification as read in the database
    const notificationId = button.dataset.notificationId;
    if (notificationId) {
        fetch('api/notifications/mark-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: notificationId }),
        })
        .then(() => {
            // Complete the fade out and remove on success
            notificationItem.style.transition = 'opacity 0.2s ease-out'; // Shorter transition
            notificationItem.style.opacity = '0';
            
            setTimeout(() => {
                notificationItem.remove();
                // Check if there are any notifications left
                checkEmptyNotifications();
            }, 200); // Shorter timeout
        })
        .catch(error => {
            // Restore the notification on error
            notificationItem.style.opacity = '1';
            console.error('Error marking notification as read:', error);
        });
    }
}

/**
 * Mark notification as read on the full notifications page
 * @param {HTMLElement} notificationItem - The notification item element
 * @param {string|number} notificationId - The notification ID
 */
function markNotificationAsReadOnPage(notificationItem, notificationId) {
    // Apply visual changes immediately (optimistic update)
    notificationItem.classList.remove('unread');
    
    // Update title and message styling immediately
    const title = notificationItem.querySelector('h6');
    const message = notificationItem.querySelector('p.mb-1');
    
    if (title) {
        title.classList.remove('fw-bold');
        title.classList.add('text-muted');
    }
    
    if (message) {
        message.classList.add('text-secondary');
    }
    
    // Remove notification badge immediately
    const badge = notificationItem.querySelector('.notification-badge-page');
    if (badge) badge.remove();
    
    // Remove the mark as read button immediately
    const markReadBtnContainer = notificationItem.querySelector('.mt-2');
    if (markReadBtnContainer) markReadBtnContainer.remove();
    
    // Update counter immediately
    updatePageNotificationCounter();
    
    // Apply visual styles immediately
    notificationItem.style.backgroundColor = '#fafafa';
    notificationItem.style.borderLeft = '5px solid #e9e9e9';
    notificationItem.style.opacity = '0.9';
    
    // Add a subtle highlight effect to show something happened
    notificationItem.style.transition = 'background-color 0.3s ease';
    const originalBackground = notificationItem.style.backgroundColor;
    notificationItem.style.backgroundColor = '#f8f9fa';
    setTimeout(() => {
        notificationItem.style.backgroundColor = originalBackground;
    }, 300);
    
    // Create form data for AJAX request
    const formData = new FormData();
    formData.append('notification_id', notificationId);
    formData.append('mark_read', '1');
    
    // Send AJAX request (after UI is already updated)
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        // Could restore the UI state here if needed
    });
}

/**
 * Update the notification badge count in dropdown
 */
function updateNotificationCount() {
    const badge = document.querySelector('.notification-badge');
    if (!badge) return;
    
    const visibleNotifications = document.querySelectorAll('.notification-item:not(.d-none)').length;
    
    // Update the badge count
    badge.textContent = visibleNotifications;
    
    // Hide badge if no notifications
    if (visibleNotifications === 0) {
        badge.style.display = 'none';
    }
}

/**
 * Update notification counter on the notifications page
 */
function updatePageNotificationCounter() {
    const counter = document.querySelector('.notification-counter');
    if (!counter) return;
    
    let countText = counter.textContent.trim();
    let count = parseInt(countText);
    if (count > 1) {
        count -= 1;
        counter.textContent = count + ' New';
    } else {
        counter.remove();
    }
}

/**
 * Check if there are any notifications and show/hide empty state
 */
function checkEmptyNotifications() {
    const notificationBody = document.querySelector('.notification-body');
    const emptyNotification = document.querySelector('.empty-notification');
    if (!notificationBody || !emptyNotification) return;
    
    const visibleNotifications = document.querySelectorAll('.notification-dropdown .notification-item:not(.d-none)').length;
    
    if (visibleNotifications === 0) {
        // Show empty notification message
        emptyNotification.classList.remove('d-none');
    } else {
        // Hide empty notification message
        emptyNotification.classList.add('d-none');
    }
}

/**
 * Initialize notification filters on the notifications page
 */
function initNotificationFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const notifications = document.querySelectorAll('.notification-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter notifications
            notifications.forEach(notification => {
                if (filter === 'all' || notification.getAttribute('data-type') === filter) {
                    notification.style.display = 'block';
                    // Add animation
                    notification.style.animation = 'none';
                    setTimeout(() => {
                        notification.style.animation = 'fadeIn 0.5s ease forwards';
                    }, 10);
                } else {
                    notification.style.display = 'none';
                }
            });
            
            // Check if there are any visible notifications in each date group
            document.querySelectorAll('.notification-group').forEach(group => {
                const visibleNotifications = group.querySelectorAll('.notification-item[style="display: block"]').length;
                const dateHeader = group.previousElementSibling;
                if (dateHeader && dateHeader.classList.contains('notification-date-header')) {
                    dateHeader.style.display = visibleNotifications > 0 ? 'block' : 'none';
                }
            });
        });
    });
}

/**
 * Initialize Mark All as Read functionality
 */
function initMarkAllAsRead() {
    const markAllButton = document.querySelector('button[name="mark_all_read"]');
    if (markAllButton) {
        markAllButton.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.text())
            .then(data => {
                // Update all unread notifications in the UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    // Remove unread class
                    item.classList.remove('unread');
                    
                    // Update title and message styling
                    const title = item.querySelector('h6');
                    const message = item.querySelector('p.mb-1');
                    
                    if (title) {
                        title.classList.remove('fw-bold');
                        title.classList.add('text-muted');
                    }
                    
                    if (message) {
                        message.classList.add('text-secondary');
                    }
                    
                    // Remove notification badge
                    const badge = item.querySelector('.notification-badge-page');
                    if (badge) badge.remove();
                    
                    // Remove the mark as read button
                    const markReadBtn = item.querySelector('.mt-2');
                    if (markReadBtn) markReadBtn.remove();
                    
                    // Ensure notification remains visible with the proper style
                    item.style.display = 'block';
                    item.style.backgroundColor = '#fafafa';
                    item.style.borderLeft = '5px solid #e9e9e9';
                    item.style.opacity = '0.9';
                });
                
                // Remove notification counter
                const counter = document.querySelector('.notification-counter');
                if (counter) counter.remove();
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        });
    }
}
