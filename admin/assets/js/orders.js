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
    const quickStatusSelects = document.querySelectorAll('.quick-status-update');
    quickStatusSelects.forEach(select => {
      select.addEventListener('change', function() {
        if (this.value === '') return;
        
        const orderId = this.getAttribute('data-order-id');
        const status = this.value;
        
        // Create form data
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', status);
        
        // Send AJAX request
        fetch('functions/orders/update_order_status.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show toast notification instead of alert
            showToast('Status Update', 'Order #' + orderId + ' status updated to ' + status.charAt(0).toUpperCase() + status.slice(1), 'success');
            
            // Update the status badge on the page
            const statusCell = this.closest('tr').querySelector('td:nth-child(6)');
            let newBadge = '';
            switch(status) {
              case 'processing':
                newBadge = '<span class="badge bg-primary">Processing</span>';
                break;
              case 'pending':
                newBadge = '<span class="badge bg-warning">Pending</span>';
                break;
              case 'shipped':
                newBadge = '<span class="badge bg-info">Shipped</span>';
                break;
              case 'delivered':
                newBadge = '<span class="badge bg-success">Delivered</span>';
                break;
              case 'cancelled':
                newBadge = '<span class="badge bg-danger">Cancelled</span>';
                break;
              default:
                newBadge = '<span class="badge bg-secondary">' + status + '</span>';
            }
            statusCell.innerHTML = newBadge;
          } else {
            // Show error toast
            showToast('Error', data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error', 'An error occurred while updating the status.', 'error');
        });
      });
    });
    
    // Function to show toast notifications
    function showToast(title, message, type) {
      // Create toast element
      const toastEl = document.createElement('div');
      toastEl.className = 'toast align-items-center border-0';
      
      // Set background color based on type
      if (type === 'success') {
        toastEl.classList.add('bg-success', 'text-white');
      } else if (type === 'error') {
        toastEl.classList.add('bg-danger', 'text-white');
      } else if (type === 'warning') {
        toastEl.classList.add('bg-warning', 'text-dark');
      } else {
        toastEl.classList.add('bg-primary', 'text-white');
      }
      
      // Set toast content
      toastEl.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            <strong>${title}</strong>: ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      `;
      
      // Add toast to container
      const toastContainer = document.querySelector('.toast-container');
      toastContainer.appendChild(toastEl);
      
      // Initialize and show toast
      const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 5000
      });
      toast.show();
      
      // Remove toast element when hidden
      toastEl.addEventListener('hidden.bs.toast', function() {
        toastContainer.removeChild(toastEl);
      });
    }
  });