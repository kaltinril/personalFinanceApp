<?php 

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
	<script type="text/javascript">
	
		$(document).ready(function() {
		
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
				//title : 'Employees', //no title
				useRp : true,
				rp : 15,
				showTableToggleBtn : false, //don't allow table to be minimized
				width : 750,
				height : 200
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
				// collect the data
				var EmpID = prompt("Please enter the Employee ID","5");
				var Name = prompt("Please enter the Employee Name","Mark");
				var PrimaryLanguage = prompt("Please enter the Employee's Primary Language","php");
				var FavoriteColor = prompt("Please enter the Employee's Favorite Color","Tan");
				var FavoriteAnimal = prompt("Please enter the Employee's Favorite Animal","Dog");

				// call the ajax to save the data to the session
				$.get('purchases.php', { Add: true, EmpID: EmpID, Name: Name, PrimaryLanguage: PrimaryLanguage, FavoriteColor: FavoriteColor, FavoritePet: FavoriteAnimal  }
					, function(){
						// when ajax returns (callback), update the grid to refresh the data
						$(".flexClass").flexReload();
				});
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
  </body>
</html>
<?php } ?>