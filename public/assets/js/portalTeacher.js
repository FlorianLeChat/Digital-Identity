$( document ).ready( function ()
{
	let faireApple = $( '#faireApple' );
	let form = $( '#generateQrCode' );
	let boutons = $( "#boutons" );

	$( faireApple ).on( "click", function ()
	{
		$(form).css("margin-top", "3rem");
		$( boutons ).fadeOut( 500 );
		$( form ).fadeIn( 1000 ); // Afficher le formulaire de création de cours
		$( faireApple ).fadeOut( 500 ); // Cacher le bouton Faire l'appel
		$( "ul" ).fadeOut( 500 ); // Cacher la liste des cours
	} );

	$( "#consulterPresence" ).on( "click", function ()
	{
		$( boutons ).css( { "flex-direction": "column" } );
		$( "#consulterPresence" ).fadeOut( 500 );
		$( "#consulterAbsence" ).fadeIn( 500 );
		$( "#liste2" ).fadeOut( 500 );

		$( "#liste1" ).fadeIn( 1000 );
		$( "#liste1" ).css( { "align-items": "left", "height": "10rem" } );
	} );

	$( "#consulterAbsence" ).on( "click", function ()
	{
		$( boutons ).css( { "flex-direction": "column" } );
		$( "#consulterAbsence" ).fadeOut( 500 );
		$( "#consulterPresence" ).fadeIn( 500 );
		$( "#liste1" ).fadeOut( 500 );

		$( "#liste2" ).fadeIn( 1000 );
		$( "#liste2" ).css( { "align-items": "left", "height": "10rem" } );
	} );
} );