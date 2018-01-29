<% require css('silverstripe/blog: client/dist/styles/main.css') %>

<div class="blog-entry content-container <% if $SideBarView %>unit size3of4<% end_if %>">

	<% include SilverStripe\\Blog\\MemberDetails %>

	<% if $PaginatedList.Exists %>
		<h2>Posts by $CurrentProfile.FirstName $CurrentProfile.Surname for $Title:</h2>
		<% loop $PaginatedList %>
			<% include SilverStripe\\Blog\\PostSummary %>
		<% end_loop %>
	<% end_if %>

	$Form
	$CommentsForm

	<% with $PaginatedList %>
		<% include SilverStripe\\Blog\\Pagination %>
	<% end_with %>

</div>

<% include SilverStripe\\Blog\\BlogSideBar %>
