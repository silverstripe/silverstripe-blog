<div id="TrackBacks_holder" class="typography">
	<h4>TrackBacks</h4>
	
	<% if TrackBacks %>
	<ul id="TrackBacks">
		<% loop TrackBacks %>
			<li>
				<a href="$Url"><% if Title %>$Title<% else %>$Url<% end_if %></a> <span class="date">on $Created.Nice</span>
				<% if Excerpt %><p class="excerpt">$Excerpt</p><% end_if %>
			</li>
		<% end_loop %>
	</ul>
	<% else %>
		<p>No TrackBacks have been submitted for this page.</p>
	<% end_if %>
	
	<a href="$TrackBackPingLink">Trackback URL for this page.</a>
	
</div>

