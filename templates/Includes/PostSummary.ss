<div class="post-summary">
	<h2>
		<a href="$Link" title="<%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>">
			<% if $MenuTitle %>$MenuTitle
			<% else %>$Title<% end_if %>
		</a>
	</h2>

	<p class="post-image">
		<a href="$Link" <%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>>
			$FeaturedImage.setWidth(795)
		</a>
	</p>

	<% if $Summary %>
		<p>$Summary
	<% else %>
		<p>$Excerpt
	<% end_if %>
			<a href="$Link">
				<%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>
			</a>
		</p>
	
	<% include EntryMeta %>
</div>
