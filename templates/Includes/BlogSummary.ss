<div class="blogSummary">
	<h2 class="postTitle"><a href="$Link" title="<% _t('VIEWFULL', 'View full post titled -') %> '$Title'">$MenuTitle</a></h2>
	<p class="authorDate"><% _t('POSTEDBY', 'Posted by') %> $Author.XML <% _t('POSTEDON', 'on') %> $Date.Long | <a href="$Link#PageComments_holder" title="View Comments Posted">$Comments.Count <% _t('COMMENTS', 'Comments') %></a></p>
	<% if TagsCollection %>
		<p class="tags">
			Tags:
			<% loop TagsCollection %>
				<a href="$Link" title="View all posts tagged '$Tag'" rel="tag">$Tag</a><% if not Last %>,<% end_if %>
			<% end_loop %>
		</p>
	<% end_if %>
	
	<p>$Content.FirstParagraph(html)</p>
	
	<p class="blogVitals"><a href="$Link#PageComments_holder" class="comments" title="View Comments for this post">$Comments.Count comments</a> | <a href="$Link" class="readmore" title="Read Full Post">Read the full post</a></p>
</div>
