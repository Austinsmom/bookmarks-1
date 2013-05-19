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
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" media="screen, handheld, projection, tv" href="style.css" />
    <script type="text/javascript" src="jquery-2.0.0.min.js"></script>
	<script type="text/javascript">
        jQuery(document).ready(function() {
            $('h2').click(function() {
                var urls = $(this).next();
                if(urls.is(':visible'))
                    urls.slideUp();
                else
                    urls.slideDown();
            });
            
            $('#search-input input').keyup(function() {
                var val = $(this).val().toLowerCase();
                if(val.length < 2) {
                    $('#search-results').html('');
                    return;
                }
                
                var links = "";
                $('div.folder a').each(function(index, item) {
                    if($(item).attr('title').toLowerCase().indexOf(val)!=-1) {
                        links += $(item).parent().html();
                    }
                });
                
                $('#search-results').html('<ul>'+links+'</ul>');
            });
        });

	</script>
</head>

<body>

<?php

include ("BookmarkParser.php");

$stack = array();
$bookmarks = array();

function myURL($data, $depth, $no) {
    global $bookmarks;
    global $stack;
    
    // back to previous folder
    while(count($stack)>$depth) {
        array_pop($stack);
    }
    
    $current = &$bookmarks;
    foreach($stack as $name)
        $current = &$current[$name];
    $current[] = $data;
}


function myFolder($data, $depth, $no) {
    global $bookmarks;
    global $stack;
    
    $depth++;
    
    while(count($stack)>=$depth) {
        array_pop($stack);
    }
    
    $folder = $data["name"];
    if(count($stack)==0 || $stack[count($stack)-1]!=$folder) {  
        $current = &$bookmarks;
        foreach($stack as $name)
            $current = &$current[$name];
        $current[$folder] = array();
        array_push($stack, $folder);
    }
    
}

$class = new BookmarkParser();
$class->parseNetscape("bookmarks.html", 0, 'myURL', 'myFolder');

?>


<div id="search-input">
    <input type="text" />
</div>
<div id="search-results">

</div>

<?PHP

function listFolder($folder) {
    echo "<ul>";
    
    foreach($folder as $key => $data) {
        echo "<li>";
    
        // folder
        if(isset($data["url"])) {
            $icon = $data["icon"];
            if(strlen(trim($icon))==0)
                $icon = "bookmark.png";
            
            if(strlen(trim($data["descr"]))>0)
                echo '<a href="'.$data["url"].'" style="background-image:url('.$icon.')" title="'.$data["descr"].'" target="_blank">'.$data["descr"].'</a>';
            
        // bookmark
        } else {
            echo "<h2>".$key."</h2>";
            listFolder($data);
        }
        
        echo "</li>";
    }
    echo "</ul>";
}


// Root Kategorien auflisten
$color = 0;
foreach($bookmarks as $key => $data) {
    if(!isset($data["url"])) {
        echo '<div class="folder">';
        echo '<h1>'.$key.'</h1>';
        listFolder($data);
        echo "</div>";
    }
}

?>


</body>
</html>
