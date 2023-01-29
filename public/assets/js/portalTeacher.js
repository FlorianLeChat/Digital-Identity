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


	$( "button[name = \"generateQrCode\"]" ).on("click", function()
		{
			const formation = $( "select[name = 'formation']" ).val();
			const matiere = $( "select[name = 'matiere']" ).val();
			const typeCours = $( "select[name = 'typeCours']" ).val();

			// Vérification des champs formulaires
			if ($("#formation option:selected").index() == 0)
				{
					$("#error1").text("Veuillez choisir une formation");
					return false;
				}
			else if ($("#matiere option:selected").index() == 0)
				{
					$("#error1").text("");
					$("#error2").text("Veuillez choisir une matière");
					return false;
				}
			else if ($("#typeCours option:selected").index() == 0)
				{
					$("#error2").text("");
					$("#error3").text("Veuillez choisir le type de cours");
					return false;
				}
			else{
				location.reload();
				return true;
			}
		})
			// $( "button[name = \"generateQrCode\"]" ).on("click", function()
			// {
			// 	checkForm();
			// })
			
			
			
} );