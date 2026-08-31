/**
 * Wbcom Shared Admin JavaScript - Complete Implementation
 * 
 * Handles all dashboard functionality and interactions
 * 
 * @package Wbcom_Shared_Admin
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Wbcom Shared Dashboard functionality
     */
    const WbcomSharedDashboard = {
        
        /**
         * Settings from localized script
         */
        settings: typeof wbcomShared !== 'undefined' ? wbcomShared : {},

        /**
         * Initialize dashboard functionality
         */
        init: function() {
            this.bindEvents();
            this.initPluginFilters();
            this.loadNewsFeed();
            this.initAnimations();
            this.handleResponsiveBreakpoints();
        },

        /**
         * Bind dashboard events
         */
        bindEvents: function() {
            // Plugin filter buttons
            $(document).on('click', '.filter-btn', this.handlePluginFilter);
            
            // News feed refresh
            $(document).on('click', '.refresh-news', this.refreshNewsFeed);
            
            // Smooth scrolling for anchor links
            $(document).on('click', 'a[href^="#"]', this.handleSmoothScroll);
            
            // Tab navigation with keyboard support
            $('.nav-tab').on('keydown', this.handleTabKeydown);
            
            // Window resize handling
            $(window).on('resize', this.debounce(this.handleResponsiveBreakpoints, 250));
        },

        /**
         * Handle plugin filter
         */
        handlePluginFilter: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const filter = $btn.data('filter');
            
            // Update active state
            $('.filter-btn').removeClass('active');
            $btn.addClass('active');
            
            // Apply filter
            WbcomSharedDashboard.applyFilter(filter);
        },

        /**
         * Apply plugin filter
         */
        applyFilter: function(filter) {
            const $cards = $('.wbcom-plugin-card');
            
            if (filter === 'all') {
                $cards.fadeIn(200);
            } else {
                $cards.each(function() {
                    const $card = $(this);
                    const cardStatus = $card.data('status');
                    
                    if (cardStatus === filter) {
                        $card.fadeIn(200);
                    } else {
                        $card.fadeOut(200);
                    }
                });
            }
        },

        /**
         * Initialize plugin filters
         */
        initPluginFilters: function() {
            // Set initial filter states
            this.updateFilterCounts();
        },

        /**
         * Update filter counts
         */
        updateFilterCounts: function() {
            const $filterBtns = $('.filter-btn');
            const $cards = $('.wbcom-plugin-card');
            
            $filterBtns.each(function() {
                const $btn = $(this);
                const filter = $btn.data('filter');
                let count;
                
                if (filter === 'all') {
                    count = $cards.length;
                } else {
                    count = $cards.filter('[data-status="' + filter + '"]').length;
                }
                
                const baseText = $btn.text().replace(/ \(\d+\)/, '');
                $btn.text(baseText + ' (' + count + ')');
            });
        },

        /**
         * Load news feed
         */
        loadNewsFeed: function() {
            const $newsFeed = $('#wbcom-news-feed');
            
            if ($newsFeed.length === 0) {
                return;
            }
            
            $.ajax({
                url: 'https://wbcomdesigns.com/wp-json/wp/v2/posts',
                data: { 
                    per_page: 5,
                    _embed: true
                },
                timeout: 10000,
                success: function(posts) {
                    WbcomSharedDashboard.renderNewsFeed(posts);
                },
                error: function() {
                    WbcomSharedDashboard.renderNewsFeedError();
                }
            });
        },

        /**
         * Render news feed
         */
        renderNewsFeed: function(posts) {
            const $newsFeed = $('#wbcom-news-feed');
            let newsHtml = '';
            
            if (posts && posts.length > 0) {
                posts.forEach(function(post) {
                    const excerpt = $('<div>').html(post.excerpt.rendered).text().trim();
                    const date = new Date(post.date).toLocaleDateString();
                    const author = post._embedded && post._embedded.author && post._embedded.author[0] 
                        ? post._embedded.author[0].name 
                        : 'Wbcom Designs';
                    
                    newsHtml += '<div class="news-item">';
                    newsHtml += '<h4><a href="' + WbcomSharedDashboard.escapeHtml(post.link) + '" target="_blank">' + 
                               WbcomSharedDashboard.escapeHtml(post.title.rendered) + '</a></h4>';
                    newsHtml += '<p>' + WbcomSharedDashboard.escapeHtml(excerpt.substring(0, 150)) + '...</p>';
                    newsHtml += '<div class="news-meta">';
                    newsHtml += '<time>' + date + '</time>';
                    newsHtml += '<span class="author">by ' + WbcomSharedDashboard.escapeHtml(author) + '</span>';
                    newsHtml += '</div>';
                    newsHtml += '</div>';
                });
                
                // Show footer
                $('.news-footer').show();
            } else {
                newsHtml = '<div class="news-empty">' +
                          '<h3>No News Available</h3>' +
                          '<p>Unable to load recent news at this time.</p>' +
                          '</div>';
            }
            
            $newsFeed.html(newsHtml);
        },

        /**
         * Render news feed error
         */
        renderNewsFeedError: function() {
            const $newsFeed = $('#wbcom-news-feed');
            const errorHtml = '<div class="news-error">' +
                             '<span class="dashicons dashicons-warning"></span>' +
                             '<h3>Unable to Load News</h3>' +
                             '<p>Please check your internet connection and try again later.</p>' +
                             '<div class="news-fallback-content">' +
                             '<h4>Stay Connected</h4>' +
                             '<ul>' +
                             '<li>Visit our <a href="https://wbcomdesigns.com/blog/" target="_blank">blog</a> for the latest updates</li>' +
                             '<li>Follow us on <a href="https://twitter.com/wbcomdesigns" target="_blank">X</a></li>' +
                             '<li>Join our <a href="https://www.facebook.com/wbcomdesigns/" target="_blank">Facebook</a> community</li>' +
                             '</ul>' +
                             '</div>' +
                             '</div>';
            $newsFeed.html(errorHtml);
        },

        /**
         * Refresh news feed
         */
        refreshNewsFeed: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const originalText = $btn.text();
            
            $btn.prop('disabled', true).text('Refreshing...');
            
            setTimeout(function() {
                WbcomSharedDashboard.loadNewsFeed();
                $btn.prop('disabled', false).text(originalText);
            }, 1000);
        },

        /**
         * Handle smooth scrolling
         */
        handleSmoothScroll: function(e) {
            const href = $(this).attr('href');
            
            if (!href || !href.startsWith('#')) {
                return;
            }
            
            const $target = $(href);
            
            if ($target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $target.offset().top - 100
                }, 500);
            }
        },

        /**
         * Handle tab keyboard navigation
         */
        handleTabKeydown: function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                e.preventDefault();
                const $tabs = $('.nav-tab');
                const currentIndex = $tabs.index(this);
                let newIndex;
                
                if (e.key === 'ArrowLeft') {
                    newIndex = currentIndex === 0 ? $tabs.length - 1 : currentIndex - 1;
                } else {
                    newIndex = currentIndex === $tabs.length - 1 ? 0 : currentIndex + 1;
                }
                
                $tabs.eq(newIndex).focus().trigger('click');
            }
        },

        /**
         * Initialize animations
         */
        initAnimations: function() {
            if (this.prefersReducedMotion()) {
                return;
            }
            
            // Fade in cards on load
            $('.wbcom-plugin-card, .premium-plugin-item, .premium-theme-item').each(function(index) {
                $(this).css('opacity', '0').delay(index * 50).animate({ opacity: 1 }, 300);
            });
            
            // Add hover animations to stats boxes
            $('.stat-box').hover(
                function() {
                    $(this).addClass('animate-hover');
                },
                function() {
                    $(this).removeClass('animate-hover');
                }
            );
        },

        /**
         * Handle responsive breakpoints
         */
        handleResponsiveBreakpoints: function() {
            const $window = $(window);
            const windowWidth = $window.width();
            const breakpoints = {
                mobile: 768,
                tablet: 1024
            };
            
            // Remove existing classes
            $('body').removeClass('wbcom-mobile wbcom-tablet wbcom-desktop');
            
            // Add appropriate class
            if (windowWidth <= breakpoints.mobile) {
                $('body').addClass('wbcom-mobile');
            } else if (windowWidth <= breakpoints.tablet) {
                $('body').addClass('wbcom-tablet');
            } else {
                $('body').addClass('wbcom-desktop');
            }
            
            // Adjust news feed layout on mobile
            if (windowWidth <= breakpoints.mobile) {
                $('.news-meta').addClass('mobile-layout');
            } else {
                $('.news-meta').removeClass('mobile-layout');
            }
        },

        /**
         * Check if user prefers reduced motion
         */
        prefersReducedMotion: function() {
            return window.matchMedia && 
                   window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        },

        /**
         * Utility function to debounce function calls
         */
        debounce: function(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        /**
         * Escape HTML for safe display
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        /**
         * Show notification message
         */
        showNotification: function(message, type = 'info', duration = 5000) {
            const noticeClass = 'notice-' + type;
            const $notice = $(
                '<div class="notice ' + noticeClass + ' wbcom-notice is-dismissible">' +
                '<p>' + this.escapeHtml(message) + '</p>' +
                '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button>' +
                '</div>'
            );
            
            $('.wbcom-dashboard h1').after($notice);
            
            // Auto-dismiss
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, duration);
            
            // Manual dismiss
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Handle errors gracefully
         */
        handleError: function(error, context) {
            console.error('[Wbcom Shared] Error in ' + context + ':', error);
            
            if (this.settings.debug) {
                this.showNotification(
                    'An error occurred in ' + context + '. Check console for details.',
                    'error'
                );
            }
        },

        /**
         * Initialize accessibility features
         */
        initAccessibility: function() {
            // Add ARIA live region for announcements
            if ($('#wbcom-live-region').length === 0) {
                $('body').append('<div id="wbcom-live-region" class="sr-only" aria-live="polite" aria-atomic="true"></div>');
            }
            
            // Improve keyboard navigation
            $('.wbcom-plugin-card, .premium-plugin-item, .premium-theme-item').attr('tabindex', '0');
            
            // Add focus indicators
            $('input, select, button, .nav-tab, [tabindex]').on('focus', function() {
                $(this).addClass('focus-visible');
            }).on('blur', function() {
                $(this).removeClass('focus-visible');
            });
        },

        /**
         * Announce to screen readers
         */
        announceToScreenReader: function(message) {
            $('#wbcom-live-region').text(message);
        },

        /**
         * Get plugin statistics for debugging
         */
        getStats: function() {
            return {
                plugins: $('.wbcom-plugin-card').length,
                activePlugins: $('.wbcom-plugin-card[data-status="active"]').length,
                newsLoaded: $('#wbcom-news-feed .news-item').length > 0,
                responsive: $('body').hasClass('wbcom-mobile') || $('body').hasClass('wbcom-tablet'),
                prefersReducedMotion: this.prefersReducedMotion()
            };
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        try {
            // Initialize main dashboard functionality
            WbcomSharedDashboard.init();
            
            // Initialize accessibility features
            WbcomSharedDashboard.initAccessibility();
            
            // Show loaded state
            $('.wbcom-dashboard').addClass('loaded');
            
        } catch (error) {
            WbcomSharedDashboard.handleError(error, 'initialization');
        }
    });

    /**
     * Handle window events
     */
    $(window).on('resize', WbcomSharedDashboard.debounce(function() {
        try {
            WbcomSharedDashboard.handleResponsiveBreakpoints();
        } catch (error) {
            WbcomSharedDashboard.handleError(error, 'window resize');
        }
    }, 250));

    /**
     * Expose object globally for debugging and extensibility
     */
    window.WbcomSharedDashboard = WbcomSharedDashboard;

})(jQuery);