$(document).ready(function() {
  let faireApple = $('#faireApple');
  let form = $('#generateQrCode');
  let boutons = $("#boutons");

  $(faireApple).on( "click",function() {
    //$(form).css("margin-top", "-15vh");
    $(boutons).css({"align-items": "left", "height": "10rem"});
    $(form).fadeIn(1000); // Afficher le formulaire de création de cours
    $(faireApple).fadeOut(500); // Cacher le bouton Faire l'appel
  });
});