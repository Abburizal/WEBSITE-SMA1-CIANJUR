// Enhanced JavaScript with modern UI/UX interactions
$(function(){
  // Smooth scroll for anchor links
  $('a[href^="#"]').on('click', function(e) {
    e.preventDefault();
    var target = $(this.getAttribute('href'));
    if (target.length) {
      $('html, body').stop().animate({
        scrollTop: target.offset().top - 80
      }, 600, 'easeInOutCubic');
    }
  });

  // Add loading states and animations to contact form
  $("#contactForm").on("submit", function(e){
    e.preventDefault();
    var $form = $(this);
    var $submitBtn = $form.find('button[type="submit"]');
    var $result = $("#formResult");
    var data = $form.serialize();

    // Enhanced validation with better UX
    var email = $("#email").val().trim();
    var nama = $("#nama").val().trim();
    var pesan = $("#pesan").val().trim();
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Clear previous results with fade out
    $result.fadeOut(200);
    
    setTimeout(function() {
      if(!nama || !email || !pesan){
        showAlert('warning', 'Semua field wajib diisi.', $result);
        return;
      }
      if(!emailRe.test(email)){
        showAlert('warning', 'Format email tidak valid.', $result);
        return;
      }

      // Show loading state
      $submitBtn.prop('disabled', true);
      $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim...');

      // Submit via AJAX with enhanced feedback
      $.post($form.attr("action"), data, function(resp){
        showAlert('success', resp || 'Pesan berhasil dikirim!', $result);
        $form[0].reset();
        
        // Add success animation
        $form.addClass('animate-success');
        setTimeout(function() {
          $form.removeClass('animate-success');
        }, 1000);
        
      }).fail(function(xhr){
        var msg = xhr.responseText || "Terjadi kesalahan. Silakan coba lagi.";
        showAlert('danger', msg, $result);
      }).always(function() {
        // Reset button state
        $submitBtn.prop('disabled', false);
        $submitBtn.html('<span class="btn-text">Kirim Pesan</span>');
      });
    }, 300);
  });

  // Enhanced alert function with animations
  function showAlert(type, message, $container) {
    var alertClass = 'alert-' + type;
    var icon = type === 'success' ? '✅' : type === 'warning' ? '⚠️' : '❌';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                   '<strong>' + icon + '</strong> ' + message +
                   '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                   '</div>';
    
    $container.html(alertHtml).fadeIn(300);
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
      setTimeout(function() {
        $container.find('.alert').fadeOut(500);
      }, 5000);
    }
  }

  // Add smooth animations for page elements
  function initAnimations() {
    // Animate cards on scroll
    $(window).on('scroll', function() {
      $('.card-hover').each(function() {
        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
        
        if (elementBottom > viewportTop && elementTop < viewportBottom) {
          $(this).addClass('animate-in');
        }
      });
    });

    // Trigger scroll event on page load
    $(window).trigger('scroll');
  }

  // Add CSS classes for animations
  $('<style>')
    .prop('type', 'text/css')
    .html(`
      .card-hover {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
      }
      .card-hover.animate-in {
        opacity: 1;
        transform: translateY(0);
      }
      .animate-success {
        animation: successPulse 1s ease;
      }
      @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
      }
      .spinner-border-sm {
        width: 1rem;
        height: 1rem;
      }
    `)
    .appendTo('head');

  // Initialize animations
  initAnimations();

  // Add focus management for accessibility
  $('button, a, input, textarea').on('focus', function() {
    $(this).addClass('focus-visible');
  }).on('blur', function() {
    $(this).removeClass('focus-visible');
  });
});
