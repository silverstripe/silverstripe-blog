<% include BlogSideBar %>
<div id="BlogContent" class="typography">
	<% include BreadCrumbs %>
	
	<div class="blogEntry">
		<h2 class="postTitle">$Title</h2>
		<p class="authorDate"><% _t('BlogEntry_ss.POSTEDBY', 'Posted by') %> $Author.XML <% _t('BlogEntry_ss.POSTEDON', 'on') %> $Date.Long | $Comments.Count <% _t('BlogEntry_ss.COMMENTS', 'Comments') %></p>
		<% if TagsCollection %>
			<p class="tags">
				 <% _t('BlogEntry_ss.TAGS', 'Tags:') %> 
				<% loop TagsCollection %>
					<a href="$Link" title="<% _t('BlogEntry_ss.VIEWALLPOSTTAGGED', 'View all posts tagged') %> '$Tag'" rel="tag">$Tag</a><% if not Last %>,<% end_if %>
				<% end_loop %>
			</p>
		<% end_if %>		
		$Content		
	</div>
	
	<% if IsOwner %><p class="edit-post"><a href="$EditURL" id="editpost" title="<% _t('BlogEntry_ss.EDITTHIS', 'Edit this post') %>"><% _t('BlogEntry_ss.EDITTHIS', 'Edit this post') %></a> | <a href="$Link(unpublishPost)" id="unpublishpost"><% _t('BlogEntry_ss.UNPUBLISHTHIS', 'Unpublish this post') %></a></p><% end_if %>
	
	$PageComments
</div>
