$(document).ready(function(){
	
	// tipso for actions
	$('.entry .action span').tipso({
		size: 'small',
		speed: 500,
		delay: 10,
		background: 'rgba(0,0,0,0.8)',
		color: '#ffffff',
		width: 'auto',
		position: 'right',
	});
	// tipso for user
	$('header .right .fa ').tipso({
		size: 'small',
		speed: 200,
		delay: 50,
		background: 'rgba(0,0,0,0.8)',
		color: '#ffffff',
		width: 'auto',
		position: 'bottom',
	});
	
	
	
	
	// User actions
	$('header .right form').hide();
	$('header .right .deluser').hide();
	
	$('header .right .showuser').click(function(){
		$('header .right .showuser').hide();
		$('header .right form').fadeIn(800);
	});
	$('header .right #form2 .formaction').click(function(){
		$('header .right #form1 input').hide();
		$('header .right #form2 select').fadeIn(800);
	});
	$('header .right #form1 .newformaction').click(function(){
		$('header .right #form2 select').hide();
		$('header .right #form1 input').fadeIn(800);
	});
	
	$('.newuser').change(function(){
		name = $(this).val();
		if(confirm("Neuer Benutzer '"+name+"' anlegen?")){
			$('#form1').submit();
		}
	});
	$('.deluser').change(function(){
		valid = $(this).val();
		name = $('.deluser option[value="'+valid+'"]').text();
		if(confirm("User '"+name+"' wirklich Löschen?")){
			$('#form2').submit();
		}
	});
	
	
	
	
	
	// Entry actions
	
	$('.action .delete').click(function(){
		
		$('#form4 .delentry').val($(this).attr("data-id"));
		
		if(confirm("Eintrag wirklich Löschen?")){
			$('#form4').submit();
		}
	});
	
	$('.button.newlist').click(function(){
		
		if(confirm("Neue Liste anlegen und aktuellen Stand übernehmen?")){
			$('#form6').submit();
		}
	});
	
	$('.panel .button.saveentry').click(function(){
		//if(confirm("Eintrag speichern?")){
			$('#form5').submit();
		//}
	});
	
	
	
	// scroll to top 
	
	$('.totop').hide();
	$(window).scroll(function(){
		if($('body').scrollTop() > 500){
			$('.totop').fadeIn();
		}
		if($('body').scrollTop() < 500){
			$('.totop').fadeOut();
		}
	});
	
	$('.totop').click(function(){
		$('body').stop().animate({scrollTop:0}, '400', 'swing');
	});



	
	// Panel 
	$('.overlay').hide();
	$('.action .edit').click(function(){
		$('.panel .header h3').text("Eintrag bearbeiten");
		eid = $(this).attr("data-id");
		from = $(this).attr("data-from");
		to = $(this).attr("data-to");
		price = $(this).attr("data-price");
		text = $(this).attr("data-text");
		
		$('.panel #form5 input[name="update"]').val(eid);
		$('.panel #form5 select[name="u-fromuser"]').val(from);
		$('.panel #form5 select[name="u-touser"]').val(to);
		$('.panel #form5 input[name="u-price"]').val(price);
		$('.panel #form5 input[name="u-text"]').val(text);
		
		
		$('.panel .con').hide();
		$('.panel .editentry').show();
		$('.overlay').fadeIn();
	});

	$('.showbalance').click(function(){
		$('.panel .header h3').text("Ausgleich");
		$('.panel .con').hide();
		$('.panel .balance').show();
		$('.overlay').fadeIn();
	});
	
	$('.close-panel').click(function(){
		$('.overlay').fadeOut();
	});
	
	
	
	
	
	
	
	setTimeout(function() {
		$('div.alert').addClass("hide");
	}, 3000);
	
	
	
	
});
