<% require themedCSS('blog', 'blog') %>

<div class="blog-entry content-container <% if $SideBarView %>unit size3of4<% end_if %>">

	<% include MemberDetails %>

	<% if $PaginatedList.Exists %>
		<h2>Posts by $CurrentProfile.FirstName $CurrentProfile.Surname for $Title:</h2>
		<% loop $PaginatedList %>
			<% include PostSummary %>
		<% end_loop %>
	<% end_if %>

	$Form
	$CommentsForm

	<% with $PaginatedList %>
		<% include Pagination %>
	<% end_with %>

</div>

<% include BlogSideBar %>
