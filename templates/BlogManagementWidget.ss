<ul>
	<% if PostLink %><li><a href="$PostLink"><% _t('BlogManagementWidget_ss.POSTNEW', 'Post a new blog entry') %></a></li><% end_if %> 
	<% if CommentLink %><li><a href="$CommentLink">$CommentText</a></li><% end_if %>
	<li><a href="Security/logout"><% _t('BlogManagementWidget_ss.LOGOUT', 'Logout') %></a></li>
</ul>
