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
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	
	<script type="text/javascript" src="../lib/flexigrid-1.1/js/flexigrid.pack.js"></script>
    <link rel="stylesheet" href="../lib/flexigrid-1.1/css/flexigrid.pack.css" />
	<style>
		body { font-size: 62.5%; }
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
		h1 { font-size: 1.2em; margin: .6em 0; }
		div#users-contain { width: 350px; margin: 20px 0; }
		div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
		div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
	</style>
	<script type="text/javascript">
	
		$(document).ready(function() {
		
			var date = $( "#date" ),
				location = $( "#location" ),
				category = $( "#category" ),
				amount = $( "#amount" ),
				allFields = $( [] ).add( date ).add( location ).add( category ).add( amount ),
				tips = $( ".validateTips" );
		
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
				url : 'purchases.php',
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
						allFields.removeClass( "ui-state-error" );
						bValid = bValid && checkLength( name, "username", 3, 16 );
						bValid = bValid && checkLength( email, "email", 6, 80 );
						bValid = bValid && checkLength( password, "password", 5, 16 );
						bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
						// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
						bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
						bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
						if ( bValid ) {
							$( "#users tbody" ).append( "<tr>" +
							"<td>" + name.val() + "</td>" +
							"<td>" + email.val() + "</td>" +
							"<td>" + password.val() + "</td>" +
							"</tr>" );
							$( this ).dialog( "close" );
						}
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					allFields.val( "" ).removeClass( "ui-state-error" );
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
				// collect the data
				/* var EmpID = prompt("Please enter the Employee ID","5");
				var Name = prompt("Please enter the Employee Name","Mark");
				var PrimaryLanguage = prompt("Please enter the Employee's Primary Language","php");
				var FavoriteColor = prompt("Please enter the Employee's Favorite Color","Tan");
				var FavoriteAnimal = prompt("Please enter the Employee's Favorite Animal","Dog");

				// call the ajax to save the data to the session
				$.get('purchases.php', { Add: true, EmpID: EmpID, Name: Name, PrimaryLanguage: PrimaryLanguage, FavoriteColor: FavoriteColor, FavoritePet: FavoriteAnimal  }
					, function(){
						// when ajax returns (callback), update the grid to refresh the data
						$(".flexClass").flexReload();
				}); */
			}
		}
		
	</script>
  </head>
  <body>
	<div id="includedLogoutForm"></div>
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
		<form>
			<fieldset>
				<label for="date">Date</label>
				<input type="date" name="date" id="date" class="text ui-widget-content ui-corner-all">
				<label for="location">Location</label>
				<input type="text" name="location" id="location" value="" class="text ui-widget-content ui-corner-all">
				<label for="category">Categpry</label>
				<input type="text" name="category" id="category" value="" class="text ui-widget-content ui-corner-all">
				<label for="amount">Amount</label>
				<input type="number" name="amount" id="amount" value="" class="text ui-widget-content ui-corner-all">
			</fieldset>
		</form>
	</div>
  </body>
</html>
<?php } ?>