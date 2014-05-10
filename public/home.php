<?php 

/*
    personalFinanceApp - https://github.com/frigidplanet/personalFinanceApp
    Copyright (C) 2014 Jeremy Harris

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

	session_start();
	
	if (!isset($_SESSION['uid'])) {
		echo 'Please <a href="../index.php">login</a> first.';
	} else {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Financials</title>
	<link rel="stylesheet" href="css/site.css" />
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	
	<script type="text/javascript" src="../lib/flexigrid-1.1/js/flexigrid.js"></script>
    <link rel="stylesheet" href="../lib/flexigrid-1.1/css/flexigrid.css" />
	<style>
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		select.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
	</style>
	<script type="text/javascript">
	
		var dialogFields;
		var testVal;
		
		window.onload = function() {
			updateLocations();
			updateCategories();
			setDatePickerToday();
		};
	
		$(document).ready(function() {
		
			var tips = $( ".validateTips" );
			
			$("#purchLoc").change(function() {				
				updateCategories();
			});
				
			//dialogFields = $( [] ).add( date ).add( location ).add( category ).add( amount );
			dialogFields = $([]).add($(".addPurchFormField"));
			
			$("#includedLogoutForm").load("utils/logoutForm.html");
			
			$("#tabs").tabs({
				heightStyle: "fill",
				create: function( event, ui ) {
					if (event.type === "tabsactivate" && ui.tab.index() === 0) {
						$('#flexId').flexReload();
						//$("#tabs").tabs( "refresh" );
						//$('#flexId').recalcLayout();
					}
				},
				activate: function( event, ui ) {
					if (event.type === "tabsactivate" && ui.newTab.index() === 0) {
						$("#tab-0").show();
						$('#flexId').flexReload();
						//$("#tabs").tabs( "refresh" );
						//$('#flexId').recalcLayout();
					} else if (event.type === "tabsactivate" && ui.newTab.index() !== 0 && ui.oldTab.index() === 0) {
						$("#tab-0").hide();
					}
				}
			});
	
			$("#flexId").flexigrid({
				url : 'getPurchases.php',
				dataType : 'json',
				colModel : [ {
						display : 'Id',
						name : 'purchase_id',
						width : 90,
						sortable : true
					}, {
						display : 'Date',
						name : 'purchase_date',
						width : 120,
						sortable : true
					}, {
						display : 'Location',
						name : 'purchase_location',
						width : 120,
						sortable : true
					}, {
						display : 'Category',
						name : 'purchase_category',
						width : 80,
						sortable : true
					}, {
						display : 'Price',
						name : 'purchase_price',
						width : 80,
						sortable : true
				} ],
				buttons : [ {
						name : 'Add',
						bclass : 'add',
						onpress : purchaseButtons
					},{
						name : 'Edit',
						bclass : 'edit',
						onpress : purchaseButtons
					},{
						name : 'Delete',
						bclass : 'delete',
						onpress : purchaseButtons
					},{
						separator : true
					} 
				],
				searchitems : [ {
						display : 'Category',
						name : 'purchase_category'
					}, {
						display : 'Date',
						name : 'purchase_date',
						isdefault : true
					}, {
						display : 'Location',
						name : 'purchase_location'
					}
				],
				striped : true,
				sortname : "purchase_id",
				sortorder : "asc",
				usepager : true,
				//title : 'Purchases', //no title
				useRp : true,
				rp : 15,
				showTableToggleBtn : false, //don't allow table to be minimized
				width : 750,
				height : 200
			});
			
			$( "#dialog-form" ).dialog({
				autoOpen: false,
				height: 375,
				width: 350,
				modal: true,
				buttons: {
					"Create": function() {
						var bValid = true;
						dialogFields.removeClass( "ui-state-error" );
						$.ajax({
							url: "addPurchase.php",
							type: 'post',
							data: dialogFields.serialize(),
							dataType: 'json',
							async: false,
							success: function(data) {
								if (data[0].success === true) {
									$( "#dialog-form" ).dialog( "close" );
									$(".flexClass").flexReload();
								} else {
									$( '#formErrorMsg' ).html( data[0].message );
								}
							}
						});
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					dialogFields.val( "" ).removeClass( "ui-state-error" );
					dialogFields.val("");
					$( '#formErrorMsg' ).empty();
					$('#purchCat > option').remove();
					setDatePickerToday();
					//$('#addPurchaseForm').tooltipster('hide');
				}
			});
		
		});
		
		function purchaseButtons(com, grid) {
			if (com == 'Delete') {
				var conf = confirm('Delete ' + $('.trSelected', grid).length + ' items?')
				if(conf){
					$.each($('.trSelected', grid),
						function(key, value){
							$.get('purchases.php', { Delete: value.firstChild.innerText}
								, function(){
									// when ajax returns (callback), update the grid to refresh the data
									$(".flexClass").flexReload();
							});
					});    
				}
			}
			else if (com == 'Edit') {
				var conf = confirm('Edit ' + $('.trSelected', grid).length + ' items?')
				if(conf){
					$.each($('.trSelected', grid),
						function(key, value){
							// collect the data
							var OrgEmpID = value.children[0].innerText; // in case we're changing the key
							var EmpID = prompt("Please enter the New Employee ID",value.children[0].innerText);
							var Name = prompt("Please enter the Employee Name",value.children[1].innerText);
							var PrimaryLanguage = prompt("Please enter the Employee's Primary Language",value.children[2].innerText);
							var FavoriteColor = prompt("Please enter the Employee's Favorite Color",value.children[3].innerText);
							var FavoriteAnimal = prompt("Please enter the Employee's Favorite Animal",value.children[4].innerText);

							// call the ajax to save the data to the session
							$.get('purchases.php', 
								{ Edit: true
									, OrgEmpID: OrgEmpID
									, EmpID: EmpID
									, Name: Name
									, PrimaryLanguage: PrimaryLanguage
									, FavoriteColor: FavoriteColor
									, FavoritePet: FavoriteAnimal  }
								, function(){
									// when ajax returns (callback), update the grid to refresh the data
									$(".flexClass").flexReload();
							});
					});    
				}
			}
			else if (com == 'Add') {
				$( "#dialog-form" ).dialog( "open" );
			}
		}
		
		function setDatePickerToday() {
			$('#purchDate').datepicker().datepicker("setDate", new Date());
		}
		
		//Use ajax to get the data from the DB
		function getPurchases() {
			var result = null;

			$.ajax({
				url: "getPurchases.php",
				type: 'get',
				dataType: 'json',
				async: false,
				success: function(data) {
					//console.log(data);
					if (data[0].success === true) {
						result = data[0].message;
					} else {
						$( '#errorMsg' ).html( data[0].message );
					}
				}
			});

			return result;
		}
		
		function updateLocations() {
		
			//get the data
			var data = getLocations();
			
			//Clear the dropdown options
			$('#purchLoc > option').remove();
			
			//console.log(data);

			//Set the first one to a blank value
			$('#purchLoc').append('<option value="" selected>Select Option...</option>');
			
			//update the grid
			$(data).each(function(index, element){
				$('#purchLoc').append(
					'<option value='+element.id+'>'+element.value+'</option>'
				);       
			})
		
		}
		
		//Use ajax to get the data from the DB
		function getLocations() {
			var result = null;

			$.ajax({
				url: "getLocations.php",
				type: 'get',
				dataType: 'json',
				async: false,
				success: function(data) {
					//console.log(data);
					if (data[0].success === true) {
						result = data[0].message;
					} else {
						$( '#errorMsg' ).html( data[0].message );
					}
				}
			});

			return result;
		}
		
		function updateCategories() {
		
			//get the data
			var data = getCategoriesByLocation();
			
			//Clear the dropdown options
			$('#purchCat > option').remove();
			
			//console.log(data);

			//update the grid
			$(data).each(function(index, element){
				$('#purchCat').append(
					'<option value='+element.id+'>'+element.value+'</option>'
				);   
			})
		
		}
		
		//Use ajax to get the data from the DB
		function getCategoriesByLocation() {
			var result = null;

			$.ajax({
				url: "getCategoriesByLocation.php",
				type: 'get',
				data: { locationId: $('#purchLoc').val() },
				dataType: 'json',
				async: false,
				success: function(data) {
					//console.log(data);
					if (data[0].success === true) {
						result = data[0].message;
					} else {
						$( '#errorMsg' ).html( data[0].message );
					}
				}
			});

			return result;
		}
		
	</script>
  </head>
  <body>
	<div id="includedLogoutForm"></div>
	<div id="errorMsg"></div>
    <div id="tabs">
      <ul>
        <li>
			<a href="#tab-0">Purchases</a>
			<!--a href="purchases.html">Purchases</a-->
        </li>
        <li>
          <a href="bills2014.html">Bills 2014</a>
        </li>
        <li>
          <a href="bills2015.html">Bills 2015</a>
        </li>
      </ul>
    </div>
	<div id="tab-0">
		<table class="flexClass" id="flexId" style="display: none"></table>
	</div>
	<div id="dialog-form" title="Add new purchase">
		<p class="validateTips">All fields are required.</p>
		<div id="formErrorMsg"></div>
		<form>
			<fieldset>
				<label for="purchDate">Date</label>
				<input name="purchDate" id="purchDate" class="addPurchFormField text ui-widget-content ui-corner-all">
				<label for="purchLoc">Location</label>
				<select name="purchLoc" id="purchLoc" class="addPurchFormField text ui-widget-content ui-corner-all"></select>
				<label for="purchCat">Category</label>
				<select name="purchCat" id="purchCat" class="addPurchFormField text ui-widget-content ui-corner-all"></select>
				<label for="purchAmt">Amount</label>
				<input type="number" name="purchAmt" id="purchAmt" value="" class="addPurchFormField text ui-widget-content ui-corner-all">
			</fieldset>
		</form>
	</div>
  </body>
</html>
<?php } ?>