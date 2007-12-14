<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>$Subject</title>		
	</head>
	<body>
		<h1>$Subject</h1>
		
		<p>Hello $Member.FirstName,</p>
		
		<p>This email confirms you signed up to the site.</p>
				
		<% if ConfirmLink %>
			<p>Please visit this link to confirm you would like to sign up:</p>
			<p><a href="$ConfirmLink" title="Confirm newsletter subscription">$ConfirmLink</a></p>
		<% end_if %>
	</body>
</html>