<div class="blogSummary">
	<h2 class="postTitle"><a href="$Link" title="<% _t('VIEWFULL', 'View full post titled -') %> '$Title'">$MenuTitle</a></h2>
	<p class="authorDate"><% _t('POSTEDBY', 'Posted by') %> $Author.XML <% _t('POSTEDON', 'on') %> $Date.FormatI18N(%x) | | <a href="$Link#PageComments_holder" title="View Comments Posted">$Comments.Count <% _t('COMMENTS', 'Comments') %></a></p>
	<% if TagsCollection %>
		<p class="tags">
			Tags:
			<% control TagsCollection %>
				<a href="$Link" title="View all posts tagged '$Tag'" rel="tag">$Tag</a><% if Last %><% else %>,<% end_if %>
			<% end_control %>
		</p>
	<% end_if %>
	
	<p>$Content.FirstParagraph(html)</p>
	
	<p class="blogVitals"><a href="$Link#PageComments_holder" class="comments" title="<% _t('BlogSummary.ss.COMMENTSTITLE', 'View Comments for this post') %>"> $Comments.Count <% _t('BlogSummary.ss.COMMENTS', 'comments') %> </a> | <a href="$Link" class="readmore" title="<% _t('BlogSummary.ss.READFULLPOSTTITLE', 'Read FULL Post') %>"><% _t('BlogSummary.ss.READFULLPOST', 'Read the full post') %></a></p>
</div>
