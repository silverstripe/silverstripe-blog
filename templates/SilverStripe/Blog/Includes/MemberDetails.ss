<section>
	<h1>$CurrentProfile.FirstName $CurrentProfile.Surname</h1>
	<div>
		<% if $CurrentProfile.BlogProfileImage %>
		<div class="profile-image">
			$CurrentProfile.BlogProfileImage.ScaleWidth(180)
		</div>
		<% end_if %>
		<div class="profile-summary">
			<p>$CurrentProfile.BlogProfileSummary</p>
		</div>
	</div>
</section>
