/**
 * KraftX Dynamic Wishlist
 */
const Wishlist = {
    items: [],

    init: function() {
        this.fetchWishlist();
        this.bindEvents();
    },

    fetchWishlist: function() {
        $.ajax({
            url: '/wishlist/fetch',
            method: 'GET',
            success: (response) => {
                if (response.success) {
                    this.items = response.items;
                    this.updateUI();
                }
            }
        });
    },

    bindEvents: function() {
        $(document).on('click', '.btn-add-wishlist, .btn-wishlist, .card-product .wishlist', (e) => {
            e.preventDefault();
            const $this = $(e.currentTarget);
            const productId = $this.data('product-id');
            
            if (!productId) {
                console.warn('Product ID missing on wishlist button');
                return;
            }

            this.toggle(productId, $this);
        });
    },

    toggle: function(productId, $btn) {
        $.ajax({
            url: '/wishlist/toggle',
            method: 'POST',
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.success) {
                    if (response.added) {
                        this.items.push(parseInt(productId));
                        this.showNotification('Added to wishlist', 'success');
                    } else {
                        this.items = this.items.filter(id => id !== parseInt(productId));
                        this.showNotification('Removed from wishlist', 'info');
                    }
                    this.updateUI();
                }
            },
            error: (xhr) => {
                if (xhr.status === 401 && xhr.responseJSON.login_required) {
                    this.showNotification('Please login to use wishlist', 'warning');
                    // Optional: Show login modal
                    if ($('#sign').length) {
                        const modal = new bootstrap.Modal(document.getElementById('sign'));
                        modal.show();
                    }
                } else {
                    this.showNotification('Something went wrong', 'error');
                }
            }
        });
    },

    updateUI: function() {
        // Update all wishlist buttons on the page
        $('.btn-add-wishlist, .btn-wishlist, .card-product .wishlist').each((i, el) => {
            const $el = $(el);
            const productId = parseInt($el.data('product-id'));
            const icon = $el.find('.icon');
            const tooltip = $el.find('.tooltip');

            if (this.items.includes(productId)) {
                $el.addClass('active addwishlist');
                icon.removeClass('icon-heart').addClass('icon-heart-filled icon-heart'); // Depending on your icons
                // If using icomoon or similar, you might need specific classes
                if (tooltip.length) tooltip.text('In Wishlist');
            } else {
                $el.removeClass('active addwishlist');
                icon.removeClass('icon-heart-filled');
                if (tooltip.length) tooltip.text('Add to Wishlist');
            }
        });

        // Update wishlist counts
        $('.wishlist-count').text(this.items.length);
        if (this.items.length > 0) {
            $('.wishlist-count').show();
        } else {
            $('.wishlist-count').hide();
        }
    },

    showNotification: function(message, type) {
        // You can use your existing notification system if you have one
        console.log(`Wishlist: ${message} (${type})`);
        window.dispatchEvent(new CustomEvent('notify', { 
            detail: { type: type, text: message } 
        }));
    }
};

$(document).ready(function() {
    Wishlist.init();
});
