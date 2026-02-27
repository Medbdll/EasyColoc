// Modal management functions
window.showInviteModal = function() {
    document.getElementById('inviteModal').classList.remove('hidden');
}

window.hideInviteModal = function() {
    document.getElementById('inviteModal').classList.add('hidden');
}

window.showCategoryModal = function() {
    document.getElementById('categoryModal').classList.remove('hidden');
}

window.hideCategoryModal = function() {
    document.getElementById('categoryModal').classList.add('hidden');
}

window.showExpenseModal = function() {
    document.getElementById('expenseModal').classList.remove('hidden');
}

window.hideExpenseModal = function() {
    document.getElementById('expenseModal').classList.add('hidden');
}

// Auto-hide notifications after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const notifications = document.querySelectorAll('.fixed.bottom-4.right-4');
        notifications.forEach(notification => {
            notification.style.display = 'none';
        });
    }, 5000);
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
        hideInviteModal();
        hideCategoryModal();
        hideExpenseModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideInviteModal();
        hideCategoryModal();
        hideExpenseModal();
    }
});
