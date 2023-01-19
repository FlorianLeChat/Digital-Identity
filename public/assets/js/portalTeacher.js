$( document ).ready( function ()
{
	let faireApple = $( '#faireApple' );
	let form = $( '#generateQrCode' );
	let boutons = $( "#boutons" );

	$( faireApple ).on( "click", function ()
	{
		//$(form).css("margin-top", "-15vh");
		$( boutons ).css( { "align-items": "left", "height": "10rem" } );
		$( form ).fadeIn( 1000 ); // Afficher le formulaire de création de cours
		$( faireApple ).fadeOut( 500 ); // Cacher le bouton Faire l'appel
		$( "ul" ).fadeOut( 500 ); // Cacher la liste des cours
	} );

	$( "#consulterPresence" ).on( "click", function ()
	{
		$( boutons ).css( { "flex-direction": "column" } );
		$( "#consulterPresence" ).fadeOut( 500 );

		$( "ul" ).fadeIn( 1000 );
		$( "ul" ).css( { "align-items": "left", "height": "10rem" } );
	} );
} );