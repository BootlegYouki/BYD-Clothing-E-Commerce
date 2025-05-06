document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr(".datepicker", {
      dateFormat: "Y-m-d",
    });
    
    // Toggle custom date fields
    window.toggleCustomDateFields = function() {
      const dateSelect = document.getElementById('date');
      const customDateFields = document.querySelectorAll('.custom-date-field');
      
      if (dateSelect.value === 'custom') {
        customDateFields.forEach(field => field.style.display = 'block');
      } else {
        customDateFields.forEach(field => field.style.display = 'none');
      }
    }
    
    // Handle order details modal
    const orderDetailsModal = document.getElementById('orderDetailsModal');
    if (orderDetailsModal) {
      orderDetailsModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const orderId = button.getAttribute('data-orderid');
        const contentDiv = document.getElementById('order-details-content');
        
        // Fetch order details
        fetch(`functions/orders/fetch_order_details.php?order_id=${orderId}`)
          .then(response => response.text())
          .then(data => {
            contentDiv.innerHTML = data;
          })
          .catch(error => {
            contentDiv.innerHTML = `<div class="alert alert-danger">Error loading order details: ${error}</div>`;
          });
      });
    }
    
    // Print order functionality
    document.getElementById('print-order').addEventListener('click', function() {
      const printContents = document.getElementById('order-details-content').innerHTML;
      const originalContents = document.body.innerHTML;
      
      document.body.innerHTML = `
        <div style="padding: 20px;">
          <h2 style="text-align: center; margin-bottom: 20px;">BYD Clothing - Order Details</h2>
          ${printContents}
        </div>
      `;
      
      window.print();
      document.body.innerHTML = originalContents;
      location.reload();
    });
    
    // Add sorting functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
      header.style.cursor = 'pointer';
      
      header.addEventListener('click', function() {
        const column = this.getAttribute('data-sort');
        let direction = 'ASC';
        
        // If this column is already sorted, toggle direction
        if (column === '<?= $sort_column ?>') {
          direction = '<?= $sort_direction ?>' === 'ASC' ? 'DESC' : 'ASC';
        }
        
        // Create URL with current filters plus new sort parameters
        let url = new URL(window.location.href);
        let params = new URLSearchParams(url.search);
        
        // Update or add sort parameters
        params.set('sort', column);
        params.set('direction', direction);
        
        // Redirect to the new URL
        window.location.href = `${url.pathname}?${params.toString()}`;
      });
    });
    
    // Handle bulk actions
    const selectAllCheckbox = document.getElementById('selectAllOrders');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    const selectedCountSpan = document.getElementById('selectedOrderCount');
    
    // Select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
      const isChecked = this.checked;
      orderCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
      });
      updateBulkActionButton();
    });
    
    // Individual checkboxes
    orderCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', updateBulkActionButton);
    });
    
    // Update bulk action button state
    function updateBulkActionButton() {
      const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
      bulkActionBtn.disabled = checkedCount === 0;
      if (selectedCountSpan) {
        selectedCountSpan.textContent = checkedCount;
      }
    }
    
    // Show bulk action modal
    bulkActionBtn.addEventListener('click', function() {
      const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
      modal.show();
    });
    
    // Quick status update
    initQuickStatusUpdate();
  });

/**
 * Initialize quick status update functionality
 */
function initQuickStatusUpdate() {
    const statusDropdowns = document.querySelectorAll('.quick-status-update');
    
    statusDropdowns.forEach(dropdown => {
        // Store original value to detect changes
        dropdown.dataset.originalValue = dropdown.value;
        
        dropdown.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            const originalStatus = this.dataset.originalValue;
            
            // Skip if no change or no value selected
            if (newStatus === originalStatus || newStatus === '') {
                return;
            }
            
            // Show loading state
            this.disabled = true;
            
            // Get the notification button container
            const notifyBtnContainer = document.querySelector(`.notify-btn-container[data-order-id="${orderId}"]`);
            
            // Send AJAX request to update status
            fetch('functions/orders/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${newStatus}&send_notification=no`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update original value
                    this.dataset.originalValue = newStatus;
                    
                    // Update status badge in the table
                    const statusCell = this.closest('tr').querySelector('td:nth-child(6)');
                    statusCell.innerHTML = getStatusBadgeHtml(newStatus);
                    
                    // Show notify button
                    if (notifyBtnContainer) {
                        notifyBtnContainer.innerHTML = `
                            <button type="button" class="btn btn-sm btn-primary notify-customer-btn" 
                                onclick="sendStatusNotification(${orderId}, '${newStatus}')">
                                <i class='bx bx-envelope'></i>
                            </button>`;
                    }
                    
                    // Show success toast
                    showToast('Order status updated successfully', 'success');
                } else {
                    // Show error toast and revert selection
                    showToast('Error: ' + (data.message || 'Failed to update status'), 'error');
                    this.value = originalStatus;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update order status', 'error');
                this.value = originalStatus;
            })
            .finally(() => {
                // Re-enable dropdown
                this.disabled = false;
            });
        });
    });
}

/**
 * Send notification to customer about status change
 */
function sendStatusNotification(orderId, status) {
    const btnContainer = document.querySelector(`.notify-btn-container[data-order-id="${orderId}"]`);
    if (btnContainer) {
      // Show loading state
      btnContainer.innerHTML = `<button type="button" class="btn btn-sm btn-primary notify-customer-btn" disabled>
          <i class='bx bx-loader-alt bx-spin'></i>
      </button>`;
    }
    
    // Send AJAX request to notify customer
    fetch('functions/orders/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&status=${status}&notify_customer=yes`
    })
    .then(response => {
        // First check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        // Then try to parse as JSON, but handle text response as error if not valid JSON
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Invalid JSON response:", text);
                throw new Error("Server returned invalid JSON response");
            }
        });
    })
    .then(data => {
        if (data.success || data.status === 'success') {
            if (btnContainer) {
                btnContainer.innerHTML = `<span class="badge bg-success d-none"><i class='bx bx-check'></i> Notification Sent</span>`;
            }
            showToast('Notification sent to customer', 'success');
        } else {
            if (btnContainer) {
                btnContainer.innerHTML = `<button type="button" class="btn btn-sm btn-primary notify-customer-btn"  
                    onclick="sendStatusNotification(${orderId}, '${status}')">
                    <i class='bx bx-envelope'></i> Retry Notification
                </button>`;
            }
            showToast('Error: ' + (data.message || 'Failed to send notification'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (btnContainer) {
            btnContainer.innerHTML = `<button type="button" class="btn btn-sm btn-primary notify-customer-btn" 
                onclick="sendStatusNotification(${orderId}, '${status}')">
                <i class='bx bx-revision'></i> Retry
            </button>`;
        }
        showToast('Failed to send notification: ' + error.message, 'error');
    });
}

/**
 * Generate HTML for status badge
 */
function getStatusBadgeHtml(status) {
    let badgeClass = 'bg-secondary';
    let statusText = status.charAt(0).toUpperCase() + status.slice(1);
    
    switch(status) {
        case 'processing':
            badgeClass = 'bg-primary';
            break;
        case 'pending':
            badgeClass = 'bg-warning';
            break;
        case 'shipped':
            badgeClass = 'bg-info';
            break;
        case 'delivered':
            badgeClass = 'bg-success';
            break;
        case 'cancelled':
            badgeClass = 'bg-danger';
            break;
    }
    
    return `<span class="badge ${badgeClass}">${statusText}</span>`;
}

/**
 * Display a toast notification
 * @param {string} message - The message to display
 * @param {string} type - The type of toast (success, error, info)
 */
function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
        console.error('Toast container not found');
        return;
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Set toast content
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    bsToast.show();
    
    // Remove from DOM after hidden
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}