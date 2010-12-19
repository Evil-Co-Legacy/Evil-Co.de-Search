var search = {
		pageNo								:			1,
		itemsPerPage						:			20,
		searchType							:			0,
		query								:			'',
		
		clearField							:			function() {
			if ($j('#query').hasClass('emptySearchField')) {
				$j('#query').removeClass('emptySearchField');
				$j('#query').val('');
			}
		},
		
		changedQueryField					:			function() {
			if ($j('#query').val() == '' && !$j('#query').hasClass('emptySearchField')) {
				$('#query').addClass('emptySearchField');
			}
		},
		
		submitSearch						:			function() {
			this.searchType = $j('#searchType').val();
			this.itemsPerPage = $j('#itemsPerPage').val();
			this.query = $j('#query').val();
			
			if ($j('#results').css('display') != 'none') $j('#results').hide('slide', { }, 'fast');
			$j('#query').attr('disabled', 'disabled');
			
			$j.ajax({
				url: 'api.php?action=Search&searchType='+search.searchType+'&page='+search.pageNo+'&itemsPerPage='+search.itemsPerPage+'&type=html&query='+escape(this.query), 
				success: function(data) {
					$j('#resultsInner').html(data);
					$j('#results').show('slide');
					$j('#query').attr('disabled', '');
				}
			});
		},
		
		changePage							:			function(pageNo) {
			this.pageNo = pageNo;
			this.submitSearch();
		},
		
		changeAdvancedSearch				:			function(typeID) {
			if (typeID > 0) {
				$j('#advancedSearchInner').html('<p class="advancedSearchLoadingMessage">'+languages['www.search.loadingAdvancedSearch']+'</p>');
				
				$j.ajax({	url:	'api.php?action=GetAdvancedSearchFields&typeID='+typeID+'&type=json',
							success: function(data, textStatus, XMLHttpRequest) {
								for(var i = 0; i < data.length; i++) {
									$j('#advancedSearchInner').append('<div class="formElement">');
									$j('#advancedSearchInner').append('<div class="formFieldLabel">');
									$j('#advancedSearchInner').append('<label for="'+data[i]['name']+'">'+data[i]['label']+'</label>');
									$j('#advancedSearchInner').append('</div>');
									$j('#advancedSearchInner').append('<div class="formField">');
									$j('#advancedSearchInner').append('<input type="text" name="'+data[i]['name']+'" class="inputText" />');
									$j('#advancedSearchInner').append('</div>');
									$j('#advancedSearchInner').append('</div>');
								}
							},
							dataType: 'json'
						});
			}
		}
};