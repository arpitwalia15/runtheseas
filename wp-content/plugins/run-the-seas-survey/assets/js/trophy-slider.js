jQuery(document).ready(function($) {
    // Trophy Slider
    var currentSlide = 0;
    var totalSlides = $('.rts-trophy-slide').length;
    
    function showSlide(index) {
        $('.rts-trophy-slide').removeClass('active');
        $('.rts-trophy-slide').eq(index).addClass('active');
        
        // Update dots
        $('.rts-slider-dot').removeClass('active');
        $('.rts-slider-dot').eq(index).addClass('active');
    }
    
    // Next/Prev buttons
    $('.rts-slider-next').on('click', function() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    });
    
    $('.rts-slider-prev').on('click', function() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    });
    
    // Dot navigation
    $('.rts-slider-dot').on('click', function() {
        var index = $(this).data('index');
        currentSlide = index;
        showSlide(currentSlide);
    });
    
    // Auto-slide
    var autoSlide = setInterval(function() {
        if (!document.hidden) {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }
    }, 5000);
    
    // Pause on hover
    $('.rts-trophy-slider').on('mouseenter', function() {
        clearInterval(autoSlide);
    }).on('mouseleave', function() {
        autoSlide = setInterval(function() {
            if (!document.hidden) {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            }
        }, 5000);
    });
    
    // Share functionality
    window.rtsShareTrophy = function() {
        $('#rts-share-modal').fadeIn();
    };
    
    window.rtsCloseShare = function() {
        $('#rts-share-modal').fadeOut();
    };
    
    window.rtsCopyLink = function() {
        var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            alert('Link copied to clipboard!');
        }).catch(function() {
            // Fallback
            var tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Link copied to clipboard!');
        });
    };
    
    // Close modal on overlay click
    $('#rts-share-modal').on('click', function(e) {
        if ($(e.target).is(this)) {
            $(this).fadeOut();
        }
    });
});