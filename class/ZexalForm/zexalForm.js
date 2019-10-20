$(document).ready(function(){
	$('head').append('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">');
	$('head').append('<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"><\/script>');
	$('head').append('<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"><\/script>');
	$('head').append('<link rel="stylesheet" type="text/css" href="Zexarel/class/ZexalForm/zexalForm.css">');

	var visitedSection = [0];

	$('#FORMID .section-button').on('click', function(){
		var d = $('.anser', $(this).parents('.carousel-item'));
		var vi = true;
		$.each(d, function(k, v){
			if(!isValid(v)){
				vi = false;
			}
		});
		if(vi){
			var n = parseInt($(this).attr("data-section"));
			$('#FORMID .carousel').carousel(n);
			visitedSection.push(n);
		}
	});

	$('#FORMID .anser').on("click focus", function(){
		$(this).parent().children('.underline').addClass('active');
	});
	$('#FORMID .anser').on("focusout", function(){
		isValid($(this));
	});

	$('#FORMID .radio-option > *').on("click", function(){
		$('input', $(this).parents('.question-radio')).prop('checked', false);
		$('.inner-radio', $(this).parents('.question-radio')).removeClass("selected");
		$('input', $(this).parents('.radio-option')).prop('checked', true);
		$('.inner-radio', $(this).parents('.radio-option')).addClass("selected");
	});
	$('#FORMID .check-option > *').on("click", function(){
		if($('input', $(this).parents('.check-option')).prop('checked')){
			$('input', $(this).parents('.check-option')).prop('checked', false);
			$('.inner-check', $(this).parents('.check-option')).removeClass("selected");
		}else{
			$('input', $(this).parents('.check-option')).prop('checked', true);
			$('.inner-check', $(this).parents('.check-option')).addClass("selected");
		}
	});

	$('#FORMID input[type=tel]').on("keydown keyup key", function(e){
		if(!(e.key >= "0" && e.key <= "9")){
			e.preventDefault();
		}
	});

	$('#FORMID .file-upload').on("click", function(){
		$(this).parents().children("input").click();
	});

	$('#FORMID input[type=file]').on("change", function(e){
		$(this).parents().children('.file-upload').hide();
		$(this).parents().children('.file-uploaded').show();
		var f = "";
		$.each(e.target.files, function(k, v){
			f += v.name + ", \n";
		});
		$(this).parents().children('.file-uploaded').attr("data-name", f.substr(0, f.length-3));
	});

	$('#FORMID .file-uploaded svg').on("click", function(){
		$(this).parents().children('.file-uploaded').hide();
		$(this).parents().children('.file-upload').show();
		$(this).parents().children('input').val("");
	});

	function isValid(obj){
		var reg = new RegExp($(obj).parents('.section-question').attr('data-valid'));
		switch($(obj).attr("type")){
			case "radio":
			case "checkbox":
				if(reg != "/.*/"){
					if($('#FORMID input[name='+$(obj).attr("name")+']:checked').lenght != 0){
						var o = $(obj).parents('.section-question');
						if($(o).hasClass('error')){
							$(o).removeClass('error');
						}
						return true;
					}else{
						var o = $(obj).parents('.section-question');
						if(!$(o).hasClass('error')){
							$(o).addClass('error');
						}
						return false;
					}
				}else {
					return true;
				}
				break;
			case "file":
				if(reg != "/.*/"){
					if($('#FORMID input[name='+$(obj).attr("name")+']')[0].files.length != 0){
						var o = $(obj).parents('.section-question');
						if($(o).hasClass('error')){
							$(o).removeClass('error');
						}
						return true;
					}else{
						var o = $(obj).parents('.section-question');
						if(!$(o).hasClass('error')){
							$(o).addClass('error');
						}
						return false;
					}
				}else{
					return true;
				}
				break;
			case "text":
			case "email":
			case "tel":
			case "number":
			default:
				if(reg.test($(obj).val())){
					var o = $(obj).parents('.section-question');
					if($(o).hasClass('error')){
						$(o).removeClass('error');
					}
					o = $(obj).parent().children('.underline');
					if($(o).hasClass('active')){
						$(o).removeClass('active');
					}
					return true;
				}else{
					var o = $(obj).parents('.section-question');
					if(!$(o).hasClass('error')){
						$(o).addClass('error');
					}
					o = $(obj).parent().children('.underline');
					if(!$(o).hasClass('active')){
						$(o).addClass('active');
					}
					return false;
				}
				break;
		}
	}
});
