<% if TrackBacks %>
	<div id="TrackBacks_holder" class="typography">
		<h4>TrackBacks</h4>
		
		<ul id="TrackBacks">
			<% control TrackBacks %>
				<li>
					<a href="$Url"><% if Title %>$Title<% else %>$Url<% end_if %></a> <span class="date">on $Created.Nice</span>
					<% if Excerpt %><p class="excerpt">$Excerpt</p><% end_if %>
				</li>
			<% end_control %>
		</ul>
		
	</div>
<% end_if %>
