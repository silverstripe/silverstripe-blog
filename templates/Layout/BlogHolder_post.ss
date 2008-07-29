<div id="left_column">
	<% include MainMenu %>
	$SideBar
</div>

<div id="BlogContent" class="blogcontent typography">
	
	<% include BreadCrumbs %>
	
		<% if isPost %> 
		
			$BlogEntryForm

	<% end_if %>
</div>