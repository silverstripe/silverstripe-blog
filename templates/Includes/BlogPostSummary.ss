<div class="post-summary">
	<% if $FeaturedImage %>
		<p class="post-image"><a href="$Link">
			$FeaturedImage.setWidth(795)
		</a></p>
	<% end_if %>
	<h2><a href="$Link">$Title</a></h2>
	<p>$Content.Summary(30,0)</p>
	<div class="post-controls">
		<p class="post-stats">
			<%t Blog.Posted 'Posted' %> $PublishDate.Ago
			 | <%t Blog.Tags 'Tags' %>: <% loop $Tags %><a href="$Link">$Title</a><%if not $Last%>,<% end_if %>  <% end_loop %>  
			 | <%t Blog.Categories 'Categories' %>: <% loop $Categories %><a href="$Link">$Title</a><%if not $Last%>,<% end_if %>  <% end_loop %>
		</p>
		<p><a href="$Link"><%t Blog.ReadMore 'Read More' %> &gt;</a></p>
	</div>
</div>
