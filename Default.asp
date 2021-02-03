<%

Function DisplayHeader(host,title,thishost,gap)

  If host=thishost Then
     Response.Write("      <td width=""" & gap & "%"" bgcolor=""#FFFFFF"" align=center>" & Chr(13))
     Response.Write("        <font face=""Arial"" Size=""2""><b>" & title & "</b>" & Chr(13))
     Response.Write("      </td>" & Chr(13))
  Else
     Response.Write("      <td width=""" & gap & "%"" bgcolor=""#CCCCCC"" align=center>" & Chr(13))
     Response.Write("        <font face=""Arial"" Size=""2""><b><a href=""" & host & """>" & title & "</a></b>" & Chr(13))
     Response.Write("      </td>" & Chr(13))
  End If

End Function


Function DisplayHeaders(Thispage)
  Response.write("    <table width=""100%"" border=""1"" borderwidth=""1"" align=""center"" cellpadding=""5"">" & Chr(13))
  Dim ret
  ret=DisplayHeader("http://traceroute","Internet benchmarking",ThisPage,"15")
  ret=DisplayHeader("http://monitor","Infrastructure monitoring",ThisPage,"17")
  ret=DisplayHeader("http://audit","PC audit",ThisPage,"13")
  ret=DisplayHeader("http://tabs","Pupil blocking system",ThisPage,"13")
  ret=DisplayHeader("http://space","Disk space",ThisPage,"10")
  ret=DisplayHeader("http://accountcontrol","Account control",ThisPage,"12")
  ret=DisplayHeader("http://stash","Room control",ThisPage,"11")
  ret=DisplayHeader("http://displayadmin","Displays",ThisPage,"7")
  Response.write("    </table>" & Chr(13))
End Function


Function IsDisabled(OU)

  Dim objuser

  Set objUser = GetObject("LDAP://CN=" & OU & ",OU=Controlled Assessments,OU=students,OU=User Accounts,DC=ad,DC=ashcombe,DC=surrey,DC=sch,DC=uk")
  
  IsDisabled = objUser.AccountDisabled

  Set objUser = Nothing


End Function

Function DisableClass(OU)

  Dim objOU

  Set objOU = GetObject("LDAP://OU=" & OU & ",OU=Controlled Assessments,OU=students,OU=User Accounts,DC=ad,DC=ashcombe,DC=surrey,DC=sch,DC=uk")  
  For Each objUser In objOU
    If objUser.class="user" then
       objUser.AccountDisabled = True
       objUser.SetInfo
    End if
  Next 
  Set objOU = Nothing 
End Function

Function EnableClass(OU)

  Dim objOU

  Set objOU = GetObject("LDAP://OU=" & OU & ",OU=Controlled Assessments,OU=students,OU=User Accounts,DC=ad,DC=ashcombe,DC=surrey,DC=sch,DC=uk")  
  For Each objUser In objOU
    If objUser.class="user" then
       objUser.AccountDisabled = False
       objUser.SetInfo
    End if
  Next 
  Set objOU = Nothing 
End Function


Dim disable,enable,user,password,IsAdmin

disable=Request.QueryString("disable")
enable=Request.QueryString("enable")

user=Request.Form("user")
password = Request.Form("password")

If user = "" Then
  user = Request.Cookies("user")
End If

If password = "" Then
  password = Request.Cookies("password")
End If

If user <> "" Then
  Response.Cookies("user") = user
End If

If password <> "" Then
  Response.Cookies("password") = password
End If

If (user <> "admin" or password <> "oldsocks") And (user <> "english" or password <> "Dickens!") And (user <> "music" or password <> "IanDury!") And (user <> "computing" or password <> "PWalters!") Then
  IsAdmin=false
Else
  IsAdmin=true
End If

Response.AddHeader "pragma", "no-cache"
Response.AddHeader "Cache-control", "no-cache, no-store, must-revalidate"
Response.AddHeader "Expires", "01 Apr 1995 01:10:10 GMT"
Response.Write("<html>" & CHR(13))
Response.Write("  <head>" & CHR(13))
Response.Write("    <title>Account Control</title>" & CHR(13))
Response.Write("    <META NAME=""datemodified"" CONTENT=""20040522"" />" & CHR(13))
Response.Write("    <META HTTP-EQUIV=""Expires"" CONTENT=""Tue, 01 Jan 1980 1:00:00 GMT"">" & CHR(13))
Response.Write("    <META NAME=""dateexpired"" CONTENT=""20051225"" />" & CHR(13))
Response.Write("    <META HTTP-EQUIV=""Pragma"" CONTENT=""no-cache"" />" & CHR(13))
Response.Write("    <STYLE>" & Chr(13))
Response.Write("      body {	background-color: #FFFFFF;" & Chr(13))
Response.Write("	margin-left: 3px;" & Chr(13))
Response.Write("	margin-top: 3px;" & Chr(13))
Response.Write("	margin-right: 3px;" & Chr(13))
Response.Write("	margin-bottom: 3px;}" & Chr(13))
Response.Write("    </STYLE>" & Chr(13))
Response.Write("  </head>" & Chr(13))
Response.Write("  <body>" & Chr(13))
Dim dh
dh=DisplayHeaders("http://accountcontrol")
If IsAdmin=false Then
  Response.Write("  <form method=""POST"" action=""Default.asp"">" & Chr(13))
  Response.Write("  <br><br><br><br>" & Chr(13))
  Response.Write("  <p align=""center"">" & Chr(13))
  Response.Write("  <font face=""arial"" size=""3""><br><br><b>Please log into Account Control below...</font><br><br>" & Chr(13))
  Response.Write("    <font face=""arial"" size=""2""><b>User ID: <input name=""user"" type=""text"" value=""" & user & """>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Password: <input name=""password"" type=""password"" value=""" & password & """>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=""submit"" value=""Submit""></b></font></form>" & Chr(13))
  If user <> "" Or password <> "" Then
    Response.Write("    <font Color=""#FF0000"" face=""Arial"" size=""4""><b><br><br>YOU'VE GOT THAT WRONG!</b></font>" & Chr(13))
  End If
  Response.Write("    </p>" & Chr(13))
  Response.Write("  </body>" & Chr(13))
  Response.Write("</html>" & Chr(13))
  Response.End
Else
  If enable <>"" Then
    EnableClass(enable)
  Else
    If disable<>"" Then
      DisableClass(disable)
    End If
  End If

  Response.Write("   <Table width=""80%"" border=""0"" align=""center""><tr><td align=""center"">" & Chr(13))
  Response.Write("  <br><br><br><br>" & Chr(13))
  Response.Write("  <p align=""center"">" & Chr(13))
  Response.Write("  <font face=""arial"" size=""3""><br><br><b>Account Control</font><br><br>" & Chr(13))
  Response.Write("   <Table width=""100%"" border=""0"" align=""center""><tr valign=""top"">") 
  If user="admin" Or user="english" Then
    Response.Write("   <td align=""center"">" & Chr(13))
    Response.Write("     <b>English</b><br><br>" & Chr(13))
    If Not IsDisabled("caeng01,OU=Class01,OU=English") Then
      Response.Write("<a href=""Default.asp?disable=Class01%2COU%3DEnglish"">Disable Class 1</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=Class01%2COU%3DEnglish"">Enable Class 1</a><br><br>" & Chr(13))
    End If
    If Not IsDisabled("caengl01,OU=Class02,OU=English") Then
      Response.Write("<a href=""Default.asp?disable=Class02%2COU%3DEnglish"">Disable Class 2</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=Class02%2COU%3DEnglish"">Enable Class 2</a><br><br>" & Chr(13))
    End If
    Response.Write("   </td>" & Chr(13))
  End If
  If user="admin" Or user="music" Then
    Response.Write("   <td align=""center"">" & Chr(13))
    Response.Write("   <b>Music</b><br><br>" & Chr(13))
    If Not IsDisabled("camusic01,OU=Class01,OU=Music") Then
      Response.Write("<a href=""Default.asp?disable=Class01%2COU%3DMusic"">Disable Class 1</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=Class01%2COU%3DMusic"">Enable Class 1</a><br><br>" & Chr(13))
    End If
    If Not IsDisabled("camus01,OU=Class02,OU=Music") Then
      Response.Write("<a href=""Default.asp?disable=Class02%2COU%3DMusic"">Disable Class 2</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=Class02%2COU%3DMusic"">Enable Class 2</a><br><br>" & Chr(13))
    End If
    If Not IsDisabled("camu01,OU=Class03,OU=Music") Then
      Response.Write("<a href=""Default.asp?disable=Class03%2COU%3DMusic"">Disable Class 3</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=Class03%2COU%3DMusic"">Enable Class 3</a><br><br>" & Chr(13))
    End If
    Response.Write("   </td>" & Chr(13))
  End If
  If user="admin" Or user="computing" Then
    Response.Write("   <td align=""center"">" & Chr(13))
    Response.Write("     <b>Computing</b><br><br>" & Chr(13))
    If Not IsDisabled("cacomp01,OU=11Ecm1,OU=Computing") Then
      Response.Write("<a href=""Default.asp?disable=11Ecm1%2COU%3DComputing"">Disable 11Ecm1</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=11Ecm1%2COU%3DComputing"">Enable 11Ecm1</a><br><br>" & Chr(13))
    End If
    If Not IsDisabled("cacomp25,OU=11Fcm1,OU=Computing") Then
      Response.Write("<a href=""Default.asp?disable=11Fcm1%2COU%3DComputing"">Disable 11Fcm1</a><br><br>" & Chr(13))
    Else
      Response.Write("<a href=""Default.asp?enable=11Fcm1%2COU%3DComputing"">Enable 11Fcm1</a><br><br>" & Chr(13))
    End If
    Response.Write("   </td>" & Chr(13))
  End If
  Response.Write("    </td></tr></table>" & Chr(13))
  Response.Write("  </body>" & Chr(13))
  Response.Write("</html>" & Chr(13))
  Response.End
End If
%>