/**
 * Admin Notifications System
 * Handles notification creation, preview, and management
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification preview functionality
    initNotificationPreview();
    
    // Initialize notification type change handler
    initNotificationTypeHandler();
    
    // Initialize recipient type radio buttons
    initRecipientTypeHandler();
    
    // Initialize character counter for message
    initCharacterCounter();
    
    // Initialize delete notification modal
    initDeleteModal();
    
    // Initialize mass notification modal
    initMassNotificationModal();
});

/**
 * Initialize the live preview for notification creation
 */
function initNotificationPreview() {
    const titleInput = document.getElementById('notification_title');
    const messageInput = document.getElementById('notification_message');
    const previewTitle = document.getElementById('preview_title');
    const previewMessage = document.getElementById('preview_message');
    
    if (titleInput && previewTitle) {
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'Notification Title';
        });
    }
    
    if (messageInput && previewMessage) {
        messageInput.addEventListener('input', function() {
            previewMessage.textContent = this.value || 'Your notification message will appear here.';
        });
    }
}

/**
 * Initialize notification type change handler
 */
function initNotificationTypeHandler() {
    const typeSelect = document.getElementById('notification_type');
    const previewElement = document.getElementById('notification_preview');
    const iconElement = previewElement ? previewElement.querySelector('.notification-icon i') : null;
    
    if (typeSelect && previewElement && iconElement) {
        typeSelect.addEventListener('change', function() {
            // Update the preview class
            previewElement.className = 'notification-preview ' + this.value;
            
            // Get icon class based on notification type
            const iconClass = getIconClassForType(this.value);
            iconElement.className = iconClass;
            
            // Update the icon container class
            const iconContainer = iconElement.closest('.notification-icon');
            if (iconContainer) {
                iconContainer.className = 'notification-icon ' + this.value;
            }
        });
    }
}

/**
 * Get the appropriate icon class for a notification type
 * @param {string} type - The notification type
 * @returns {string} - The icon CSS class
 */
function getIconClassForType(type) {
    switch(type) {
        case 'order_status':
        case 'order_shipped':
            return 'bx bx-package';
        case 'order_delivered':
            return 'bx bx-check-double';
        case 'promotion':
            return 'bx bx-heart';
        case 'account':
            return 'bx bx-user';
        case 'review_reminder':
            return 'bx bx-star';
        case 'new_release':
            return 'bx bx-gift';
        default:
            return 'bx bx-bell';
    }
}

/**
 * Initialize recipient type radio buttons
 */
function initRecipientTypeHandler() {
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    const specificUserContainer = document.getElementById('specific_user_container');
    
    if (recipientRadios.length && specificUserContainer) {
        recipientRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'specific') {
                    specificUserContainer.style.display = 'block';
                    document.getElementById('user_id').setAttribute('required', 'required');
                } else {
                    specificUserContainer.style.display = 'none';
                    document.getElementById('user_id').removeAttribute('required');
                }
            });
        });
    }
}

/**
 * Initialize character counter for notification message
 */
function initCharacterCounter() {
    const messageInput = document.getElementById('notification_message');
    const messageCount = document.getElementById('message_count');
    
    if (messageInput && messageCount) {
        messageInput.addEventListener('input', function() {
            const count = this.value.length;
            messageCount.textContent = count;
            
            if (count > 450) {
                messageCount.classList.add('text-warning');
            } else {
                messageCount.classList.remove('text-warning');
            }
            
            if (count >= 500) {
                messageCount.classList.add('text-danger');
            } else {
                messageCount.classList.remove('text-danger');
            }
        });
    }
    
    const massMessageInput = document.getElementById('mass_notification_message');
    const massMessageCount = document.getElementById('mass_message_count');
    
    if (massMessageInput && massMessageCount) {
        massMessageInput.addEventListener('input', function() {
            const count = this.value.length;
            massMessageCount.textContent = count;
            
            if (count > 450) {
                massMessageCount.classList.add('text-warning');
            } else {
                massMessageCount.classList.remove('text-warning');
            }
            
            if (count >= 500) {
                massMessageCount.classList.add('text-danger');
            } else {
                massMessageCount.classList.remove('text-danger');
            }
        });
    }
}

/**
 * Initialize delete notification modal
 */
function initDeleteModal() {
    const deleteButtons = document.querySelectorAll('.delete-notification');
    const deleteModal = document.getElementById('deleteNotificationModal');
    const deleteIdInput = document.getElementById('delete_notification_id');
    
    if (deleteButtons.length && deleteModal && deleteIdInput) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');
                deleteIdInput.value = notificationId;
                
                // Show the modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            });
        });
    }
}
