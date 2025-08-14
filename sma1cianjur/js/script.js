// Enhanced JavaScript with modern UI/UX interactions
$(function(){
  // Generate CSRF token for security
  function generateCSRFToken() {
    const timestamp = Date.now().toString();
    const randomBytes = Array.from(crypto.getRandomValues(new Uint8Array(16)));
    const randomString = randomBytes.map(byte => byte.toString(16).padStart(2, '0')).join('');
    return btoa(timestamp + randomString).replace(/[+/=]/g, '');
  }
  
  // Add CSRF token to contact form
  if ($('#contactForm').length) {
    const csrfToken = generateCSRFToken();
    sessionStorage.setItem('csrf_token', csrfToken);
    $('#contactForm').prepend('<input type="hidden" name="csrf_token" value="' + csrfToken + '">');
    
    // Add character counter for message field
    $('#pesan').on('input', function() {
      const currentLength = $(this).val().length;
      const charCountElement = $('#char-count');
      if (charCountElement.length) {
        charCountElement.text(currentLength);
        
        if (currentLength > 900) {
          charCountElement.addClass('text-warning').removeClass('text-danger');
        } else if (currentLength >= 1000) {
          charCountElement.addClass('text-danger').removeClass('text-warning');
        } else {
          charCountElement.removeClass('text-warning text-danger');
        }
      }
    });
  }
  
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
    
    // Enhanced validation with better UX and security
    var email = $("#email").val().trim();
    var nama = $("#nama").val().trim();
    var pesan = $("#pesan").val().trim();
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var nameRe = /^[a-zA-Z\s\.]+$/;
    
    // Clear previous validation states
    $form.find('.is-invalid').removeClass('is-invalid');
    
    // Clear previous results with fade out
    $result.fadeOut(200);
    
    setTimeout(function() {
      var hasError = false;
      
      // Enhanced validation
      if(!nama || nama.length < 2 || nama.length > 100) {
        $('#nama').addClass('is-invalid');
        showAlert('warning', 'Nama harus diisi dan antara 2-100 karakter.', $result);
        hasError = true;
      } else if(!nameRe.test(nama)) {
        $('#nama').addClass('is-invalid');
        showAlert('warning', 'Nama hanya boleh mengandung huruf, spasi, dan titik.', $result);
        hasError = true;
      }
      
      if(!email || !emailRe.test(email) || email.length > 100) {
        $('#email').addClass('is-invalid');
        showAlert('warning', 'Email tidak valid atau terlalu panjang.', $result);
        hasError = true;
      }
      
      if(!pesan || pesan.length < 10 || pesan.length > 1000) {
        $('#pesan').addClass('is-invalid');
        showAlert('warning', 'Pesan harus diisi dan antara 10-1000 karakter.', $result);
        hasError = true;
      }
      
      if(hasError) return;

      // Show loading state
      $submitBtn.prop('disabled', true);
      $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim...');

      // Submit via AJAX with enhanced security and feedback
      $.ajax({
        url: $form.attr("action"),
        type: "POST",
        data: $form.serialize(),
        dataType: 'json',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).done(function(response){
        if (response.success) {
          showAlert('success', response.message || 'Pesan berhasil dikirim!', $result);
          $form[0].reset();
          
          // Reset character count
          $('#char-count').text('0');
          
          // Generate new CSRF token
          const newToken = generateCSRFToken();
          sessionStorage.setItem('csrf_token', newToken);
          $form.find('input[name="csrf_token"]').val(newToken);
          
          // Add success animation
          $form.addClass('animate-success');
          setTimeout(function() {
            $form.removeClass('animate-success');
          }, 1000);
        } else {
          showAlert('danger', response.message || 'Terjadi kesalahan. Silakan coba lagi.', $result);
        }
      }).fail(function(xhr){
        var msg = "Terjadi kesalahan. Silakan coba lagi.";
        try {
          var response = JSON.parse(xhr.responseText);
          if (response.message) {
            msg = response.message;
          }
        } catch(e) {
          // Use default message
        }
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
