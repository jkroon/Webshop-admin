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
	
	var optionsBtn = '#optionsBtn';
	var priceBtn = '#priceBtn';
	
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

	newOne = $('#product_opts div.head_option:last').attr('id');

	if (!newOne) {
		newOne = 0;
	} else {
		newOne = parseInt(newOne.split('_')[2]);
	}
	
	newOption = newOne + 1;
	
	// De nieuwe HTML wordt aangemaakt
	html = '<div class="head_option" id="head_option_'+newOption+'">';
	html = html + '<div class="head_option_opts" id="option_'+newOption+'"><input type="text" name="post[Product][options]['+newOption+'][name]" value="Titel van de optie" />';
	html = html + '<a href="#" onclick="deleteOption('+newOption+'); return false" style="float: right"><img src="/images/delete_option_button.png" /></a></div>';
	html = html + '<table cellpadding="0" cellspacing="0" id="options_'+newOption+'"><tr id="subOption_'+newOption+'_0">';
	html = html + '<td class="td1"><b>Naam</b></td>';
	html = html + '<td class="td2"><b>Prijs</b></td>';
	html = html + '<td class="td3"><b>Prijs optie</b></td>';
	html = html + '<td class="td4"><b>Artikelnummer</b></td>';
	html = html + '<td class="td5"><b>Verwijderen</b></td>';
	html = html + '</tr></table>';
	html = html + '<div class="head_options_add"><a href="#" title="" onclick="addNewOption('+newOption+'); return false"><img src="/images/add_option_button.png" /></a></div>';
	html = html + '</div>';

	// De html wordt in de division gezet
	$('#product_opts').append(html);
	
	// Er wordt een nieuwe optie toegevoegd
	addNewOption(newOption);
	
	return false;
}

function addNewOption(id) {

	option_id = $('#options_'+id+' tr:last').attr('id').split('_');
	option_id = parseInt(option_id[2]) + 1;

	html = '<tr id="subOption_'+id+'_'+option_id+'"><td><input type="text" name="post[Product][options]['+id+']['+option_id+'][name]" class="opt1" /></td>';
	html = html + '<td><input type="text" name="post[Product][options]['+id+']['+option_id+'][price]" class="opt2 suboptprice" /></td>';
	html = html + '<td><select name="post[Product][options]['+id+']['+option_id+'][type]" class="opt3"><option value="1">Vast</option><option value="2">Meerprijs</option></select>';
	html = html + '</td><td><input type="text" name="post[Product][options]['+id+']['+option_id+'][article_id]" class="opt4" /></td>';
	html = html + '<td><a href="#" onclick="deleteSubOption('+id+', '+option_id+'); return false" title="Verwijderen" style="margin-left: 30px"><img src="/images/delete_icon.png" /></a></td></tr>';
	
	$('#options_'+id).append(html);
	
	createEvents();
	
	return false;
}


function deleteSubOption(parent_id, id) {
	
	$('#subOption_'+parent_id+'_'+id).remove();
	
	if ($('#optErr_'+parent_id+'_'+id).length > 0) {
		$('#optErr_'+parent_id+'_'+id).remove();
	}
	
	return false;
	
}

function deleteOption(id) {

	if (confirm('Weet u zeker dat u deze optie wilt verwijderen ?')) {
		$('#head_option_'+id).remove();
	}
	
	return false;

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



function createError(id1, id2, field, error) {
	
	if ($('#subOption_'+id1+'_'+id2).attr('class') == 'trError') {
		$('#optErr_'+id1+'_'+id2+' span').append('<br />'+error)
	} else {
		$('#subOption_'+id1+'_'+id2).addClass('trError');	
		$('#subOption_'+id1+'_'+id2).after('<tr class="trError" id="optErr_'+id1+'_'+id2+'"><td colspan="5"><span>'+error+'</span></td></tr>');
	}
	
}


function createEvents() {
	
	$('.suboptprice').blur(function() {
		if ($(this).val() == '0') {
			$(this).val('0.00');
		}
	});
	
}