[INCLUDE header]


[START overview]
     <!-- Categorieen -->
     
     	<div id="category_info">
     		<h1>Categorie&euml;n &amp; subcategorie&euml;n</h1>
     		
     		<div class="info_flash" style="margin-top: 15px">
     			<p>Op deze pagina kunt u categorie&euml;en en subcategorie&euml;n toevoegen, bewerken of verwijderen. Meer informatie hierover kunt u vinden in onze online handleiding (<u><b>FAQ</b></u>).</p>
     		</div>
     	</div>
     
          <div class="cat_outer">
               <div class="cat_content">
                    <div class="cat_search">
                         <span>Categorieen</span>
                    </div>

                    <div class="cat_inner">
                    	<div id="categoryOuter"> </div>

						<div class="catStats">
							<span>Er werden <b>{category_count}</b> categorie&euml;n gevonden</span>
							
							<div class="catStatsPages" id="categoryStats">
								<a href="#" title="" onclick="updatePage('back'); return false"><img src="{http}images/back_icon.png" alt="" class="no_float" /> Vorige</a>
								<p>Pagina <b>1</b> van {category_pageTotal}</p>
								<a href="#" title="" onclick="updatePage('next'); return false">Volgende <img src="{http}images/next_icon.png" alt="" class="no_float" /></a>
							</div>
						</div>

                    </div>
               </div>
          </div>
     <!-- Categorieen -->

	<div style="width: 100%; height: 30px">&nbsp;</div>

     <!-- Subcategorieen -->
          <div class="cat_outer">
               <div class="cat_content">
                    <div class="cat_search">
                         <span>Subcategorie&euml;n</span>
                    </div>

                    <div class="cat_inner">
                         <ul>
                              <li class="li1"><a href="">Categorienaam</a></li>
                              <li class="li2"><a href="">Bewerken - Verwijderen</a></li>
                         </ul>

                         <ul class="odd">
                              <li class="li1"><a href="">Categorienaam</a></li>
                              <li class="li2"><a href="">Bewerken - Verwijderen</a></li>
                         </ul>

                         <ul>
                              <li class="li1"><a href="">Categorienaam</a></li>
                              <li class="li2"><a href="">Bewerken - Verwijderen</a></li>
                         </ul>

                         <ul class="odd">
                              <li class="li1"><a href="">Categorienaam</a></li>
                              <li class="li2"><a href="">Bewerken - Verwijderen</a></li>
                         </ul>

                         <ul>
                              <li class="li1"><a href="">Categorienaam</a></li>
                              <li class="li2"><a href="">Bewerken - Verwijderen</a></li>
                         </ul>

                    </div>
               </div>
          </div>
     <!-- Subcategorieen -->
     
     [START index_json]
     <script type="text/javascript">
     	var obj = $.parseJSON('{json}');
     	var currentPage = 1;
     	
     	$(document).ready(function() {
     		updateCategories();
     	});
     	
     	function updateCategories() {
     		$('#categoryOuter').html('');
     	
	     	$.each(obj['items'], function(key, value) {
	
				// De html wordt aangemaakt
				html = '<ul><li class="li1"><a href="">'+value['Category']['name']+'</a></li><li class="li2">';
	            html = html + '<a href="/categories/edit/'+value['Category']['id']+'/"><img src="{http}images/edit_icon.gif" alt="" /></a>';
	            html = html + '<a href="/categories/delete/'+value['Category']['id']+'/" style="margin: 0 0 0 7px"><img src="{http}images/delete_icon.png" alt="" /></a></li></ul>'; 
	            
	            // De html wordt in de division geplaatst
	            $('#categoryOuter').append(html);    		
	
	     	});
	     	
	     	$('#categoryOuter ul:odd').addClass('odd');
     	}
     	
     	
     	function updatePage(type) {
     	
     		if (type == 'next') {
     			newPage = currentPage + 1;
     		} else {
     			newPage = currentPage - 1;
     		}
     		
     		
 			$.ajax({
 				url: '/categories/getcategories/'+newPage+'/',
 				dataType: 'json',
 				success: function(response) {
 					
 					if (response['success']) {
 						obj = response;
 						updateCategories();
 						
 						if (type == 'next') {
 							currentPage = currentPage + 1;
 						} else {
 							currentPage = currentPage - 1;
 						}
 						
 						$('#categoryStats b').html(currentPage);
 					}
 					
 				}
 			});
     	
     	}
     </script>
     [END index_json]

[END overview]

[START form]
<div class="form">
     <h1>Categorie {action}</h1>
     {form}
</div>
[END form]

[INCLUDE footer]