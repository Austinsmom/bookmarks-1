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
	
	<!-- Required CSS --> 
	<link rel="stylesheet" type="text/css" href="style/folders/tree.css"> 
	<link type="text/css" rel="stylesheet" href="style/treeview.css"> 
	<link rel="stylesheet" type="text/css" href="style/folder/css/folders/tree.css"> 	

	<style type="text/css" title="currentStyle">
		body {
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:11px;
		}
		
		.ygtvlabel {
			color:#000000;
		}
		
		#bookmarks {
			width:700px;
			display:block;
		}
		
		img {
			border: 0px;
			height: 16px;
			width: 16px;
		}
	</style> 

	
	<!-- Dependency source files -->  
	<script src="js/yahoo-min.js"></script> 
	<script src="js/event-min.js"></script> 
	
	<!-- TreeView source file -->  
	<script src="js/treeview-min.js"></script> 
	
	<script type="text/javascript">

		function createTree() {
			var tree;
			tree = new YAHOO.widget.TreeView("bookmarks");
			
			var root = tree.getRoot(); 

			<?php

			include ("BookmarkParser.php");
			
			if(!$_POST['search']) {
				function myURL($data, $depth, $no) {
				
					if($depth==0)
						$out = "root";
					else
						$out = "f".$depth;
					
					if($data["icon"])
						$outIcon = "<img src='".$data["icon"]."' />";
					else
						$outIcon = "";
						
					echo "myobj = { label: \"" . $outIcon  . " " . utf8_decode($data["descr"]) . "\", href:\"".utf8_decode($data["url"])."\", target:\"window\" };\n"; 
					echo "new YAHOO.widget.TextNode(myobj, $out, false);\n";
				}

				function myFolder($data, $depth, $no) {
					if($depth==0)
						$out = "root";
					else
						$out = "f".$depth;
					echo "var f".($depth+1)." = new YAHOO.widget.TextNode(\"".utf8_decode($data["name"])."\", $out, false);\n";
				}
			} else {
				function myURL($data, $depth, $no) {
					if(strpos(strtolower($data['descr']), strtolower($_POST['search']))!==FALSE) {
						if($data["icon"])
							$outIcon = "<img src='".$data["icon"]."' />";
						else
							$outIcon = "";
							
						echo "myobj = { label: \"" . $outIcon  . " " . utf8_decode($data["descr"]) . "\", href:\"".utf8_decode($data["url"])."\", target:\"window\" };\n"; 
						echo "new YAHOO.widget.TextNode(myobj, root, false);\n";
					}
				}

				function myFolder($data, $depth, $no) {}
			}
			
			$class = new BookmarkParser();
			$class->parseNetscape("bookmarks.html", 0, 'myURL', 'myFolder');

			?>
			
			

			tree.draw(); 
		}
		
		
	</script>
</head>

<body>
<div style="margin-bottom:10px;width:700px;">
<form action="<? echo $_PHP_SELF; ?>" method="post">
	<input type="text" name="search" value="<?PHP echo $_POST['search']; ?>" style="width:110px" />
	<input type="submit" value="search";
</form>
</div>
<div id="bookmarks"></div>

<script type="text/javascript">
	createTree();
</script>


</body>
</html>
