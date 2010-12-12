[INCLUDE header]

	[START index]
		<div id="product_index">
			<h1>Producten</h1>
			
			<div style="width: 100%; margin-top: 10px">
				<a href="{http}products/add/" title="">Nieuw product toevoegen</a>
			</div>
			
			<div id="product_options">
				<ul>
					<li id="li_detailView" class="{detailView}"><a href="#" title="Detail weergave" onclick="switchView('detail'); return false">Detailweergave</a></li>
					<li id="li_listView" class="{listView}"><a href="#" title="Lijst weergave" onclick="switchView('list'); return false">Lijstweergave</a></li>
				</ul>
			</div>
			
			<div id="list_overview" style="{listDiv}"> </div>
			
			<div id="detail_overview" style="{detailDiv}"> </div>
			
			
			<div id="pages_overview">
				Er zijn in totaal {products_count} producten gevonden. <br />
				<a href="#" onclick="updatePage('back'); return false" title="Vorige pagina" class="no_float"><img src="{http}images/back_icon.png" class="no_float" /> Vorige pagina</a>
				&nbsp; &nbsp; Pagina <b>1</b> van {products_pages} &nbsp; &nbsp;
				<a href="#" title="Volgende pagina" onclick="updatePage('next'); return false" class="no_float">
					<span class="no_float">Volgende pagina</span>
					<img src="{http}images/next_icon.png" class="no_float" />
				</a>
			</div>
			
			[START index_json]
			<script type="text/javascript">
				var obj = jQuery.parseJSON('{json}');
				var currentPage = 1;

				$(document).ready(function() {

					updateIndex();

				});
			</script>
			[END index_json]
			
		</div>		
	[END index]
	
	
	

	[START add]
		<div id="product_form">
			
			<div class="prdTitle">
				<h2>Algemene informatie</h2>
			</div>
			
			<form action="" method="post" enctype="multipart/form-data">
				<fieldset>
					
					{inputPrdName}
					{inputPrdSelect}
					{inputOnline}
					
					
					<div style="width: 100%; height: 50px"> &nbsp; </div>
					
					<div class="prdTitle">	
						<h2>Product prijs</h2>
					</div>				
					
					<!--  Vaste product prijs -->
					<div id="product_price">
					
						<span style="margin-top: 12px"><b>Let op!</b> Prijzen dient u altijd <b>exclusief</b> BTW in te voeren, voor meer info klik <u>hier</u>.</span>
					
						{inputPrdPrice}
					
					</div>
					<!-- Vaste product prijs -->
					
					
					<div class="prdTitle" style="margin-top: 50px">
						<h2>Product opties</h2>
					</div>
					
					<div style="margin-top: 10px" id="optionselector">						
						<div style="width: 100%; margin-top: 6px">
							<input type="radio" name="post[Product][use_options]" value="false" onclick="setOptions(false);" {noUseOptions}>
							<span style="margin-left: 10px">Geen productopties gebruiken voor dit product</span>
						</div>
						
						<div style="width: 100%; margin-top: 11px">
							<input type="radio" name="post[Product][use_options]" onclick="setOptions(true);" value="true" style="margin-top: 2px" {useOptions}>
							<span style="margin-left: 10px; margin-top: 2px">Productopties gebruiken voor dit product</span>
						</div>
					</div>
					
					<div id="product_outer" style="margin-top: 15px; {product_outer_style}">
						
						<span style="margin-top: 12px"><b>Let op!</b> Prijzen dient u altijd <b>exclusief</b> BTW in te voeren, voor meer info klik <u>hier</u>.</span>
					
						
						<div id="product_opts">
						
							[START option]
								<div class="head_option" id="head_option_{option_id}">
									<div class="head_option_opts" id="option_{option_id}">
										<input type="text" name="post[Product][options][{option_id}][name]" value="{name}" />
										<a href="#" onclick="deleteOption({option_id}); return false" style="float: right">
											<img src="/images/delete_option_button.png" />
										</a>
									</div>
									
									<table cellpadding="0" cellspacing="0" id="options_{option_id}">
										<tr id="subOption_{option_id}_0">
											<td class="td1"><b>Naam</b></td>
											<td class="td2"><b>Prijs</b></td>
											<td class="td3"><b>Prijs optie</b></td>
											<td class="td4"><b>Artikelnummer</b></td>
											<td class="td5"><b>Verwijderen</b></td>
										</tr>
										
										[START sub_option]
											<tr id="subOption_{option_id}_{suboption_id}">
												<td>
													<div id="parent_{option_id}_{suboption_id}_name">
														<input type="text" name="post[Product][options][{option_id}][{suboption_id}][name]" value="{name}" class="opt1" />
													</div>
												</td>
											
												<td>
													<div id="parent_{option_id}_{suboption_id}_price">
														<input type="text" name="post[Product][options][{option_id}][{suboption_id}][price]" value="{price}" class="opt2 suboptprice" />
													</div>
												</td>
												
												<td>
													<div id="parent_{option_id}_{suboption_id}_type">
														<select name="post[Product][options][{option_id}][{suboption_id}][type]" class="opt3">
															<option value="1">Vast</option>
															<option value="2">Meerprijs</option>
														</select>
													</div>
												</td>
												
												<td>
													<div id="parent_{option_id}_{suboption_id}_article_id">
														<input type="text" name="post[Product][options][{option_id}][{suboption_id}][article_id]"  value="{article_id}" class="opt4" />
													</div>
												</td>
												
												<td>
													<a href="#" onclick="deleteSubOption({option_id}, {suboption_id}); return false" title="Verwijderen" style="margin-left: 30px">
														<img src="/images/delete_icon.png" />
													</a>
												</td>
											</tr>
										[END sub_option]
										
									</table>
									
									<div class="head_options_add">
										<a href="#" title="" onclick="addNewOption({option_id}); return false">
											<img src="/images/add_option_button.png" />
										</a>
									</div>
								</div>	
							[END option]
							
							<script type="text/javascript">
								$(document).ready(function() {
									createEvents();
								});
							</script>
						
						</div>
							
							<a href="#" title="" onclick="addOption(); return false" style="margin-top: 15px">
								<img src="{http}/images/add_option.png" />
							</a>

							<div style="width: 100%; height: 22px"> &nbsp; </div>
							
					</div>
									
					<!-- Spacer -->
					<div style="width: 100%; height: 50px"> &nbsp; </div>
					<!-- Spacer -->
					
								
					[START upload_image]		
					<div class="prdTitle">
						<h2>Product afbeeldingen</h2>
					</div>
					
					<div class="formDivPlaceholder" style="margin-top: 8px">
						<div id="uploadedImages">
						
							<div id="upload_data">
								[START uploaded_image]
									<div class="uploaded_image" id="uploadedImg{image_id}">
										<div style="width: 100%">
											<img src="{http}uploads/{filename}" height="100" width="100" />
										</div>
										<a href="#" title="Verwijderen" onclick="deleteImage({image_id}); return false">
											<img src="{http}images/delete_icon.png" style="margin-top: 5px" alt="Verwijderen" />
											<span style="margin-left: 10px; margin-top: 4px; color: #323232">Verwijderen</span>
										</a>
									</div>
								[END uploaded_image]
							</div>
							
							<div id="uploader_que" style="display: none">
								Uploader que
							</div>
						</div>
						
						<div id="imageUploader">
							<div style="float: right; margin-right: 20px" class="no_float">
								<input type="file" id="file_upload" name="post[Product][file]" />
							</div>
						</div>
						
					</div>
					
					<div style="width: 100%; height: 50px"> &nbsp; </div>
					
					<script type="text/javascript">
					$(document).ready(function() {
						$('#file_upload').uploadify({
							'uploader'  : '{http}js/uploadify.swf',
							'buttonImg' : '{http}images/browse_button.png',
							'width'     : 95,
							'height'    : 29,
							'script'    : '{http}products/upload/',
							'cancelImg' : '/uploadify/cancel.png',
							'scriptData': {'post[Product_image][product_id]' : '{id}', 'session_id' : '{session_id}'},
							'auto'      : true,
							'multi'		: true,
							'fileDataName' : 'post[Product_image][file]',
							'removeCompleted' : true,
							'queueID'	: 'uploader_que',
							'onSelect'  : function() { openQue() },
							'onComplete' : function(event, ID, fileObj, response, data) { handleResponse(response) },
							'onAllComplete' : function() { updateImages() }
						});
					});
					</script>
					[END upload_image]
					
					
					[START id_block]
						<input type="hidden" name="post[Product][id]" value="{id}" />
					[END id_block]
					
							
					<div class="prdTitle">									
						<h2>Product omschrijving</h2>
					</div>
					
					
					{inputDescription}
					
					<script type="text/javascript">
						$(function() {
							$('#editor').wysiwyg();
						});
					</script>
					
					
					<div class="prdTitle" style="margin-top: 50px">
						<h2>Overige opties</h2>
					</div>
					
					<div style="margin: 20px 0 0 0">
						<h3 style="font-size: 13px">BTW Tarief</h3>
						
						<div style="width: 100%; margin-top: 6px">
							<input type="radio" name="post[Product][use_tax]" value="false" {localTax}>
							<span style="margin-left: 10px">Standaard BTW tarief gebruiken (als ingesteld in configuratie)</span>
						</div>
						
						<div style="width: 100%; margin-top: 11px">
							<input type="radio" name="post[Product][use_tax]" value="true" style="margin-top: 2px" {customTax}>
							<span style="margin-left: 10px; margin-top: 2px">Zelf tarief invullen:</span>
							<input name="post[Product][tax]" id="inputProductBtw" style="width: 50px; margin-left: 10px" value="{tax}" type="text">
							<span style="margin-left: 5px; margin-top: 2px">%</span>
						</div>
						
						[START tax_error]
						<div id="divProductName" class="formDivPlaceholder formError">
							<span style="margin-bottom: 8px">U heeft dit veld incorrect ingevuld, wanneer het BTW tarief bijvoorbeeld <b>19%</b> betreft vult u in <b>19</b></span>
						</div>
						[END tax_error]
					</div>
					
					
					<div style="margin: 20px 0 0 0">
						<h3 style="font-size: 13px">Gewicht</h3>
						
						{inputWeight}
						
						<span style="width: 100%; margin-top: 2px; font-size: 11px">Laat dit veld leeg als u geen gebruik maakt van verzendkosten.</span>
					</div>
					
					<div style="margin: 25px 0 0 0">
						<h3 style="font-size: 13px">Voorraadbeheer</h3>
						
						{inputUseStock}
					</div>
					
					
					
					[START formSubmit]
					<div class="formDivPlaceholder">
						<input type="submit" style="padding: 10px 20px; margin-top: 15px; float: right" value="{button}" />
					</div>
					[END formSubmit]
					
				</fieldset>
			</form>
			
			[START json_errors]
				<script type="text/javascript">
					var errors = jQuery.parseJSON('{json}');

					$.each(errors, function(key, val) {
						$.each(val, function(key1, val1) {
							$.each(val1, function(key2, val2) {
								createError(key, key1, key2, val2);
							});
						});
					});
				</script>
			[END json_errors]
			
			[START newOptions]
				<script type="text/javascript">
					$(document).ready(function() {
						addOption();
					});
				</script>
			[END newOptions]
			
			
			</div>
		</div>
	[END add]

[INCLUDE footer]