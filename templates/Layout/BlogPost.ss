<div class="content-container <% if $Menu(2) || SideBarView %>unit size3of4<% end_if %>">
    <article>
        <h1>$Title</h1>
		<% if $FeaturedImage %>
			<p>$FeaturedImage.setWidth(795)</p>
		<% end_if %>
        <div class="content">$Content</div>
		<p class="post-stats">
			<%t Blog.Posted 'Posted' %> $PublishDate.Ago
			 | <%t Blog.Tags 'Tags' %>: <% loop $Tags %><a href="$Link">$Title</a><%if not $Last%>,<% end_if %>  <% end_loop %>  
			 | <%t Blog.Categories 'Categories' %>: <% loop $Categories %><a href="$Link">$Title</a><%if not $Last%>,<% end_if %>  <% end_loop %>
		</p>
    </article>
        $Form
        $PageComments
</div>
<% include BlogSidebar %>
