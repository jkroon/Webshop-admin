function deleteProduct(id) {
	
	if (confirm('Weet u zeker dat u dit product wilt verwijderen?')) {
		$.ajax({
			url : '/products/delete/'+id+'/',
			dataType : 'json',
			success: function(data) {
				if (data['success']) {
					$('#prdListItem_'+id).remove();
					
					// De even en oneven rijen worden aangepast
					$('#list_overview ul:odd').addClass('ulOdd');
					$('#list_overview ul:even').removeClass('ulOdd');
				} else {
					alert('Het product kon niet worden verwijderd,');
				}
			}
		});
	}
	
}


function switchPrice(type) {
	optionsBtn = '#optionsBtn';
	priceBtn = '#priceBtn';
	
	optionsObj = '#product_outer';
	priceObj = '#product_price'
	
	if (type == 'options') {
		if ($(optionsObj).css('display') == 'none') {
			$(optionsBtn).addClass('viewAct');
			$(priceBtn).removeClass('viewAct');
		
			$(priceObj).css('display', 'none');
			$(optionsObj).css('display', 'block');

			$('#options').val('true');
		}
	} else if(type == 'price') {
		if ($(priceObj).css('display') == 'none') {
			$(priceBtn).addClass('viewAct');
			$(optionsBtn).removeClass('viewAct');
			
			$(optionsObj).css('display', 'none');
			$(priceObj).css('display', 'block');

			$('#options').val('false');
		}					
	}
	
}


function addOption() {
	html = '<div class="prdOption" id="prdOption'+currentOption+'">';
	html = html + '<div class="prdFormDiv1"><label>Naam van optie</label><input type="text" name="post[Product][priceOptions]['+currentOption+'][name]"></div>';
	html = html + '<div class="prdFormDiv1"><label>Prijs</label><input type="text" name="post[Product][priceOptions]['+currentOption+'][price]" /></div>';
	html = html + '<div class="prdFormDiv1"><label>Standaard gebruiken</label><input type="checkbox" name="post[Product][priceOptions]['+currentOption+'][custom]" value="true" /></div>';
	html = html + '<div class="prdFormDiv1" id="optionDel'+currentOption+'"><a href="#" title="Optie verwijderen" onclick="deleteOption('+currentOption+'); return false">Optie verwijderen</a></div>';
	html = html + '</div>';

	$('#product_opts').append(html);
	
	// De current option wordt opgeteld
	currentOption = currentOption + 1;
	
	// Het aantal velden wordt opgehaald
	if (!currentDelete) {
		options = $('.prdOption');

		if (options.length > 1) {
			id = $('.prdOption:first').attr('id');
			id = id.substr(9);
			
			html = '<div class="prdFormDiv1" id="optionDel'+id+'"><a href="#" title="Optie verwijderen" onclick="deleteOption('+id+'); return false">Optie verwijderen</a></div>';

			$('.prdOption:first').append(html);
			currentDelete = true;
		}
	}
}

function deleteOption(id) {
	$('#prdOption'+id).slideUp(400, function() {
		$('#prdOption'+id).remove();
		
		
		options = $('.prdOption');				
		
		if (options.length < 2) {
			id = $('.prdOption:first').attr('id');
			id = id.substr(9);
			
			$('#optionDel'+id).remove();
			currentDelete = false;
		}
	});
}



function openQue() {
	$('#uploader_que').css('display', 'block');
}

function updateImages() {
	$('#uploader_que').fadeOut(600);
}

function deleteImage(id) {

	// De ajax request wordt uitgevoerd
	$.ajax({
		url: '/products/deleteimage/'+id+'/',
		dataType: 'json',
		success: function(data) {
			
			if (data['success']) {
				$('#uploadedImg'+id).remove();
			} else {
				alert('De afbeelding kon niet verwijderd worden');
			}
			
		}
	});
}

function handleResponse(data) {

	data = jQuery.parseJSON(data);

	if (!data['success']) {
		alert(data['error']);
	} else {

		// De html wordt klaargemaakt
		html = '<div class="uploaded_image" id="uploadedImg'+data['image_id']+'">';
		html = html + '<div style="width: 100%">';
	    html = html + '<img src="/uploads/'+data['image']+'" height="100" width="100" />';
		html = html + '</div>';
		html = html + '<a href="#" title="Verwijderen" onclick="deleteImage('+data['image_id']+'); return false">';
		html = html + '<img src="/images/delete_icon.png" style="margin-top: 5px" alt="Verwijderen" />';
		html = html + '<span style="margin-left: 10px; margin-top: 4px; color: #323232">Verwijderen</span>';
	    html = html + '</a>';
		html = html + '</div>';

		// De html wordt in de div toegevoegd
		$('#upload_data').append(html);
	}	
	
}

function switchView(type) {
	detailBtn = '#li_detailView';
	listBtn = '#li_listView';
	
	detailObj = '#detail_overview';
	listObj = '#list_overview';
	
	if (type == 'detail') {
		if ($(detailObj).css('display') == 'none') {
			$(detailBtn).addClass('viewAct');
			$(listBtn).removeClass('viewAct');
			
			
			$(listObj).fadeOut(450, function() {
				$(detailObj).fadeIn(450);
			});
			
			$.ajax({
				url: '/products/setview/detail/'
			});
		}
	} else if (type == 'list') {
		if ($(listObj).css('display') == 'none') {
			$(listBtn).addClass('viewAct');
			$(detailBtn).removeClass('viewAct');
			
			$(detailObj).fadeOut(450, function() {
				$(listObj).fadeIn(450);
			});
			
			$.ajax({
				url: '/products/setview/none/'
			});
		}
	}
	
	return false;
}

function updateIndex() {
	if (obj['success']) {
		
		// De divisions worden leeggemaakt
		$('#list_overview').html('');
		$('#detail_overview').html('');
		
		$.each(obj['items'], function(key, value) {
			$.each(value, function(key1, val1) {
		
				// De HTML voor het lijstoverzicht wordt aangemaakt
				html = '<ul class="{class}" id="prdListItem_'+val1['id']+'">';
				html = html + '<li class="listLi1">'+val1['name']+'</li>';
				html = html + '<li class="listLi2">';
				html = html + '<a href="/products/edit/'+val1['id']+'/" title="Bewerken"><img src="/images/edit_icon.gif" alt="" /></a>';
				html = html + '<a href="#" onclick="deleteProduct('+val1['id']+');" title="Verwijderen" style="margin-left: 10px"><img src="/images/delete_icon.png" alt="" /></a>';
				html = html + '</li>';
				html = html + '</ul>';
		
				$('#list_overview').append(html);
		
		
				// De afbeelding wordt bepaald
				if (val1['image']) {
					image = '/uploads/3__' + val1['image'];
				} else {
					image = '/images/no_img_110.png';
				}
				
				
				// De HTML voor het detailoverzicht wordt aangemaakt
				html = '<ul><li class="detailLi1"><img src="'+image+'" alt="" width="110" height="110" />';
				html = html + '</li><li class="detailLi2"><div class="prdDetailInfo">';
				html = html + '<span><b>'+val1['name']+'</b></span><span>Prijs: &euro; '+val1['price']+'</span>';
				html = html + '</div></li><li><div class="prdDetailOpts">';
				html = html + '<a href="/products/edit/'+val1['id']+'/" title=""><img src="/images/edit_icon.gif" alt="" /></a>';
				html = html + '<a href="#" title="" style="margin-left: 10px"><img src="/images/delete_icon.png" alt="" /></a>';
				html = html + '</div></li></ul>';
		
				$('#detail_overview').append(html);
				
			});
		});
		
		$('#list_overview ul:odd').addClass('ulOdd');
		$('#detail_overview ul:odd').addClass('ulOdd');
	}
	
}

function updatePage(action) {
	
	if (action == 'next') {
		newPage = currentPage + 1;
	} else {
		newPage = currentPage - 1;
	}
	
	$.ajax({
		url: '/products/get/'+newPage+'/',
		dataType: 'json',
		success: function(response) {
			if (response['success']) {
				obj = response;
				updateIndex();
				
				currentPage = newPage;
				$('#pages_overview b').html(newPage);
			}
		}
	});
	
	return false;
	
}