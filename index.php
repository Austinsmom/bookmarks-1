<?PHP
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");
	header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>bookmarks</title>
	
	<style type="text/css" title="currentStyle">
		body {
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:11px;
		}
	</style> 

</head>

<body>
<a href="javascript:window.sidebar.addPanel('Lesezeichen auf <?PHP echo $_SERVER['SERVER_NAME']; ?>','http://<?PHP echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>bookmarks.php','');">Hier klicken um ein Lesezeichen zu erstellen: http://<?PHP echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>bookmarks.php</a>. 
<br />
Wenn dieses dann ausgewählt wird, werden deine Lesezeichen in der Seitenleiste geöffnet!
</body>
</html>
