// js/script.js - interaksi sederhana dan validasi
$(function(){
  // Simple form submit with AJAX (progressive enhancement)
  $("#contactForm").on("submit", function(e){
    e.preventDefault();
    var $form = $(this);
    var data = $form.serialize();

    // basic front-end validation
    var email = $("#email").val().trim();
    var nama = $("#nama").val().trim();
    var pesan = $("#pesan").val().trim();
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!nama || !email || !pesan){
      $("#formResult").html('<div class="alert alert-warning">Semua field wajib diisi.</div>');
      return;
    }
    if(!emailRe.test(email)){
      $("#formResult").html('<div class="alert alert-warning">Format email tidak valid.</div>');
      return;
    }

    // submit via AJAX
    $.post($form.attr("action"), data, function(resp){
      $("#formResult").html('<div class="alert alert-success">'+resp+'</div>');
      $form[0].reset();
    }).fail(function(xhr){
      var msg = "Terjadi kesalahan. Coba lagi.";
      if(xhr.responseText) msg = xhr.responseText;
      $("#formResult").html('<div class="alert alert-danger">'+msg+'</div>');
    });
  });
});
