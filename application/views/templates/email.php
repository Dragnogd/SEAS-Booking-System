<html>
	<body style="background-color:#E9E9E9"><div>
		<center>
			<table width="600" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px">
				<tbody>
					<tr>
						<td valign="middle" style="text-align:center; height:50px; color:#FFF; background-color:#343a40; padding-left:20px; font-size:28px; font-weight:bold; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif"><?php echo $TYPE; ?> #<?php echo $BOOKINGID; ?> <?php echo $ACTION; ?></td>
					</tr>
					<tr>
						<td valign="top" style="color:#444; background-color:#FFF; padding:20px; font-size:14px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif"><?php echo $MESSAGE; ?></b><br><br>
							<b>Date</b><br><?php echo $BOOKINGDATE; ?><br><br>
							<b>Time</b><br><?php echo $BOOKINGTIME; ?><br><br>
							<b>Resources</b><br><?php echo $ASSETS; ?><br><br>
							<b>Additional Details</b><br><?php echo $ADDITIONALDETAILS; ?><br><br>
							<center>If you have any queries about this booking, please reply directly to this email.</center>
						</td>
					</tr>
					<tr>
						<td valign="top" style="color:#999; padding:20px; text-align:center; font-size:12px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif"><p>SEAS Booking System by Ryan Coombes 2018-2021</p></td>
					</tr>
				</tbody>
			</table>
		</center>
	</body>
</html>