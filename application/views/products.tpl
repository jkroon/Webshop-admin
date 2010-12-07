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
			
			<div id="list_overview" style="{listDiv}">
				
			</div>
			
			<div id="detail_overview" style="{detailDiv}">

			</div>
			
			
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
					
					
					<div style="width: 100%; height: 50px"> &nbsp; </div>
					
					<div class="prdTitle">	
						<h2>Product prijs / opties</h2>
					</div>					
					
					<div id="product_options">
						<ul>
							<li id="priceBtn" class="{fixedPriceActive}"><a href="#" title="" onclick="switchPrice('price')">Vaste prijs</a></li>
							<li id="optionsBtn" class="{optionsPriceActive}"><a href="#" title="" onclick="switchPrice('options')">Verschillende prijzen</a></li>
						</ul>
					</div>
					
					<div id="productOptsContent">
						<div style="padding-left: 15px">
						
							<span style="margin-top: 12px"><b>Let op!</b> Prijzen dient u altijd <b>exclusief</b> BTW in te voeren, voor meer info klik <u>hier</u>.</span>
						
							<div id="product_outer" style="{useOptionsPrice}">
								<div id="product_opts">
								
									<div class="head_option">
										<div class="head_option_opts">
											<input type="text" name="" value="Titel van de optie" />
										</div>
										
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td class="td1"><b>Naam</b></td>
												<td class="td2"><b>Prijs</b></td>
												<td class="td3"><b>Prijs optie</b></td>
												<td class="td4"><b>Artikelnummer</b></td>
												<td class="td5"><b>Verwijderen</b></td>
											</tr>
											
											<tr>
												<td><input type="text" name="option_1_1_name" class="opt1" /></td>
												<td><input type="text" name="option_1_1_price" class="opt2" /></td>
												<td>
													<select name="option_1_1_price_option" class="opt3">
														<option value="">Vast</option>
														<option value="">Meerprijs</option>
													</select>
												</td>
												<td><input type="text" name="option_1_1_article_id" class="opt4" /></td>
												<td>Verwijderen</td>
											</tr>
											
											<tr>
												<td><input type="text" name="option_1_1_name" class="opt1" /></td>
												<td><input type="text" name="option_1_1_price" class="opt2" /></td>
												<td>
													<select name="option_1_1_price_option" class="opt3">
														<option value="">Vast</option>
														<option value="">Meerprijs</option>
													</select>
												</td>
												<td><input type="text" name="option_1_1_article_id" class="opt4" /></td>
												<td>Verwijderen</td>
											</tr>
										</table>
									</div>	
									
									
								</div>
								
								<a href="#" title="" onclick="addOption(); return false">Optie toevoegen</a>

								<div style="width: 100%; height: 22px"> &nbsp; </div>
							</div>
						</div>
						</div>
						</div>
						<script type="text/javascript">
							var currentOption = 1;
						</script>
						<input type="hidden" name="post[Product][options]" id="options" value="{inputOptionsHidden}" />
									
					
					<div style="width: 100%; height: 50px"> &nbsp; </div>
					
								
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
					
					[START formSubmit]
					<div class="formDivPlaceholder">
						<input type="submit" style="padding: 10px 20px; margin-top: 15px; float: right" value="{button}" />
					</div>
					[END formSubmit]
					
				</fieldset>
			</form>
			
			<script type="text/javascript">
				var currentOption = 1;
			</script>
			
		</div>
	[END add]

[INCLUDE footer]