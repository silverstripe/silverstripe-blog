<div id="banner">
	<div class="standard"><!-- --></div>
</div>

<div id="content">
	
	<div class="container typography">
		<% include PageTitle %>

		<div class="clear"><!-- --></div>
		
			<% if Tag %>
				<h3><% _t('VIEWINGTAGGED', 'Viewing entries tagged with') %> '$Tag'</h3>
			<% end_if %>

			<% if BlogEntries %>
				<% control BlogEntries %>
						<% include BlogSummary %>
				<% end_control %>
			<% else %>
				<h3><% _t('NOENTRIES', 'There are no blog entries') %></h3>
			<% end_if %>
				<% include BlogPagination %>
				<% include BlogSideBar %>
				<% include BreadCrumbs %>
		</div>
		
		<% control ContentElements %>
			<div class="section clearfix" id="$Anchor">
			    $ContentReplaceIcon
			</div>
		<% end_control %>
	
		<div class="section faint-border">
			<div class="col10 right">
				$BottomText
			</div>
		</div>
</div>