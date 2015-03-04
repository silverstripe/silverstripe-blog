<% require css(blog/css/blog.css) %>

<div class="content-container <% if SideBarView %>unit size3of4<% end_if %>">
    <article>
        <h1>$Title</h1>
        <div class="content">$Content</div>
        <% loop $PaginatedList %>
			<% include BlogPostSummary %>
		<% end_loop %>
    </article>
        $Form
        $PageComments
    <% with $PaginatedList %>
		<% include Pagination %>
	<% end_with %>
</div>
<% if SideBarView %><% include BlogSidebar %><% end_if %>
