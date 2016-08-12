(function($){
	'use strict';

	function apSanitizeTitle(str) {
	  str = str.replace(/^\s+|\s+$/g, ''); // trim
	  str = str.toLowerCase();

	  // remove accents, swap ñ for n, etc
	  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
	  var to   = "aaaaaeeeeeiiiiooooouuuunc------";

	  /*for (var i=0, l=from.length ; i<l ; i++) {
	  	str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	  }*/

	  str = str.replace(/\s+/g, '-') // collapse whitespace and replace by -
	    .replace(/-+/g, '-'); // collapse dashes

	    return str;
	}

	function apAddLabel(str, container){
		str = str.replace(/,/g, '');
		str = str.trim();
		str = apSanitizeTitle(str);

		if( str.length > 0 ){

			var htmlTag = {
				element : 'li',
				class : 'ap-labelssugg-item',
				itemValueClass : 'ap-label-item-value',
				button : {
					class : 'ap-label-add',
					icon : 'apicon-plus',
				},
				input : '',
				accessibilityText : apLabelsTranslation.addLabel
			}

			// Add label to the main container (holder list),
			// Else add label to a specific container (suggestion list)
			if(!container){

				var container = '#ap-labels-holder';
				htmlTag.button.class = 'ap-label-remove';
				htmlTag.button.icon = 'apicon-x';
				htmlTag.input = '<input type="hidden" name="labels[]" value="'+str+'" />';
				htmlTag.accessibilityText = apLabelsTranslation.deleteLabel;

				var exist_el = false;
				$(container).find('.'+htmlTag.class).find('.'+htmlTag.itemValueClass).each(function(index, el) {
					if(apSanitizeTitle($(this).text()) == str)
						exist_el = $(this);
				});
				if (exist_el !== false) { // If the element already exist, stop and dont add label
					exist_el.animate({opacity: 0}, 100, function(){
						exist_el.animate({opacity: 1}, 400);
					});
					return;
				}

				if (!$('#labels').is(':focus'))
					$('#labels').val('').focus();

				$('#ap-labels-suggestion').hide();

				// Message for screen reader
				// Timeout used to resolve a bug with JAWS and IE...
				setTimeout(function() {
					$('#ap-labels-aria-message').text(str + " " + apLabelsTranslation.labelAdded);
				}, 250);
			}

			var html = $('<'+htmlTag.element+' class="'+htmlTag.class+'" title="'+htmlTag.accessibilityText+'"><button role="button" class="'+htmlTag.button.class+'"><span class="'+htmlTag.itemValueClass+'">'+str+'</span><i class="'+htmlTag.button.icon+'"></i></button>'+htmlTag.input+'</'+htmlTag.element+'>');
			html.appendTo(container).fadeIn(300);

		}
	}

	function apLabelsSuggestion(value){
		if(typeof window.labelsquery !== 'undefined'){
			window.labelsquery.abort();
		}
		window.labelsquery = jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action:'ap_labels_suggestion',
				q: value
			},
			context:this,
			dataType:'json',
			success: function(data){
				AnsPress.site.hideLoading(this);

				console.log(data);

				$('#ap-labels-suggestion').html('');

				if(!data.status)
					return;

				if (!$('#ap-labels-suggestion').is(':visible')) {
					$('#ap-labels-suggestion').show();
				}

				if(data['items']){
					$.each(data['items'], function(index, val) {
						var holderItems = [];
						$("#ap-labels-holder .ap-label-item-value").each(function() {
							holderItems.push($(this).text())
						});
						if ($.inArray(val, holderItems)<0) // Show items that was not already inside the holder list
							apAddLabel(val, '#ap-labels-suggestion');
					});
				}

				// Message for screen reader
				// Timeout used to resolve a bug with JAWS and IE...
				setTimeout(function() {
					$('#ap-labels-aria-message').text(apLabelsTranslation.suggestionsAvailable);
				}, 250);
			}
		});
	}

	$(document).ready(function(){

		$('#labels').on('apAddNewLabel',function(e){
			e.preventDefault();
			apAddLabel($(this).val().trim(','));
			$(this).val('');
		});

		$('#labels').on('keydown', function(e) {
			if(e.keyCode == 13) { // Prevent submit form on Enter
			  	e.preventDefault();
			  	return false;
			}
			if(e.keyCode == 38 || e.keyCode == 40) {
				var inputs = $('#ap-labels-suggestion').find('.ap-label-add');
				var focused = $('#ap-labels-suggestion').find('.focus');
				var index = inputs.index(focused);

				if(index != -1) {
					if(e.keyCode == 38) // up arrow
						index--;
					if(e.keyCode == 40) // down arrow
						index++;
				}
				else {
					if(e.keyCode == 38) // up arrow
						index = inputs.length-1;
					if(e.keyCode == 40) // down arrow
						index = 0;
				}

				if (index >= inputs.length)
					index = -1;

				inputs.removeClass('focus');

				if(index != -1) {
					inputs.eq(index).addClass('focus');
					$(this).val(inputs.eq(index).find('.ap-label-item-value').text());
				}
				else {
					$(this).val($(this).attr('data-original-value'));
				}
			}
		});

		$('#labels').on('keyup focus', function(e) {
			e.preventDefault();
			var val = $(this).val().trim();
			clearTimeout(window.labeltime);
			if(e.keyCode != 9 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) { // Do nothing on Tab and arrows keys
				if(e.keyCode == 13 || e.keyCode == 188 ) { // "Enter" or ","
					clearTimeout(window.labeltime);
					$(this).trigger('apAddNewLabel');
				} else {
					$(this).attr('data-original-value', $(this).val());
					window.labeltime = setTimeout(function() {
						apLabelsSuggestion(val);
					}, 200);
				}
			}
		});

		$('#ap-labels-suggestion').delegate('.ap-labelssugg-item', 'click', function(e) {
			apAddLabel($(this).find('.ap-label-item-value').text());
			$(this).remove();
		});

		$('body').on('click focusin', function(e) {
			if ($('#ap-labels-suggestion').is(':visible') && $(e.target).parents('#ap-labels-add').length <= 0)
			  	$('#ap-labels-suggestion').hide();
		});

		$('body').delegate('.ap-labelssugg-item', 'click', function(event) {
			var itemValue = $(this).find('.ap-label-item-value').text();

			// Message for screen reader
			// Timeout used to resolve a bug with JAWS and IE...
			setTimeout(function() {
				$('#ap-labels-aria-message').text(itemValue + " " + apLabelsTranslation.labelRemoved);
			}, 250);

			$(this).remove();
			$('#ap-labels-list-title').focus();
		});

		// Message used by screen reader to get suggestions list or a confirmation when a label is added
		$('body').append('<div role="status" id="ap-labels-aria-message" aria-live="polite" aria-atomic="true" class="sr-only"></div>');
	})

})(jQuery)
