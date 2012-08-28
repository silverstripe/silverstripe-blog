<% if DisplayMode == month %>
	<ul class="archiveMonths">
		<% loop Dates %>
			<li>
				<a href="$Link">
					$Date.Format(F) $Date.Year
				</a>
			</li>
		<% end_loop %>
	</ul>
<% else %>
	<ul class="archiveYears">
		<% loop Dates %>
			<li>
				<a href="$Link">
					$Date.Year<% if not Last %>,<% end_if %>
				</a>
			</li>
		<% end_loop %>
	</ul>
<% end_if %>