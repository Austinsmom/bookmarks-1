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
    <link type="image/x-icon" rel="shortcut icon" href="favicon.ico" />
    <link type="text/css" rel="stylesheet" media="screen, handheld, projection, tv" href="style.css" />
</head>

<body>

    <?php

        include ("BookmarkParser.php");

        $stack = array();
        $bookmarks = array();

        // will be executed by bookmarkparser for bookmarks
        function myURL($data, $depth, $no) {
            global $bookmarks;
            global $stack;
            
            // back to previous folder
            while(count($stack)>$depth)
                array_pop($stack);
            
            $current = &$bookmarks;
            foreach($stack as $name)
                $current = &$current[$name];
            $current[] = $data;
        }

        // will be executed by bookmarkparser for folders
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

        // parse bookmarks
        $class = new BookmarkParser();
        $class->parseNetscape("bookmarks.html", 0, 'myURL', 'myFolder');

    ?>

    <div id="wrapper">
    
        <!-- folder list -->
        <div id="folders">
            <!-- search box -->
            <div id="search-input">
                <input type="text" placeholder="search term" />
            </div>

            <?PHP
                // list all bookmarks and recursive the subfolders with its bookmarks
                function listFolder($folder) {
                    // counts all bookmarks of current and subfolders
                    $counter = 0;
                    
                    $html = "<ul>";
                    
                    foreach($folder as $key => $data) {
                        
                        // bookmark
                        if(isset($data["url"])) {
                            $counter++;
                            
                            $icon = $data["icon"];
                            if(strlen(trim($icon))==0)
                                $icon = 'bookmark.png';
                            
                            if(strlen(trim($data["descr"]))>0)
                                $html = $html . '<li class="bookmark"><a href="'.$data["url"].'" style="background-image:url('.$icon.')" title="'.$data["descr"].'" target="_blank">'.$data["descr"].'</a></li>';
                            
                        // folder
                        } else {
                            $listResult = listFolder($data);
                            $counter += $listResult["counter"];
                            
                            $html = $html . '<li>';
                            $html = $html . '<h2>' . $key . ' <span>' . $listResult["counter"] . '</span></h2>';
                            $html = $html . $listResult["html"];
                            $html = $html . '</li>';
                        }
                    }
                    
                    $html = $html . "</ul>";
                    return array("counter" => $counter,
                                 "html" => $html);
                }

                $result = listFolder($bookmarks);
                echo $result["html"];
                $counter = $result["counter"];
            ?>
            
            <div id="counter"><?PHP echo $counter; ?> bookmarks</div>
        </div>

        <!-- bookmarks -->
        <ul id="bookmarks">
            
        </ul>
    
    </div>
    
    <!-- javascripts -->
    <script type="text/javascript" src="jquery-2.1.1.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            // folder selection
            $('h2').click(function() {
                // set active
                $(this).parent().parent().find('ul').slideUp();
                $('#folders').find('.active').removeClass('active');
                $(this).parent().addClass('active');
                
                // open submenue
                var subelements = $(this).next();
                if (subelements.find('li:not(.bookmark)').length>0)
                    subelements.slideDown();
                    
                // show bookmarks
                var urls = $(this).next().find('> li.bookmark');
                $('#bookmarks').html('');
                $('#bookmarks').append(urls.clone());
            });
            
            // search
            $('#search-input input').keyup(function() {
                var val = $(this).val().toLowerCase();
                if(val.length < 2) {
                    $('#bookmarks').html('');
                    return;
                }
                
                $('#folders').find('.active').removeClass('active');
                
                $('#bookmarks').html('');
                $('li.bookmark').each(function(index, item) {
                    if($(item).find('a').attr('title').toLowerCase().indexOf(val)!=-1) {
                        $('#bookmarks').append($(item).clone());
                    }
                });
            });
            
            // set color
            $('h2').each(function(index, item) {
                var r = (Math.round(Math.random()* 127) + 127).toString(16);
                var g = (Math.round(Math.random()* 127) + 127).toString(16);
                var b = (Math.round(Math.random()* 127) + 127).toString(16);
                $(this).css('borderLeft', '7px solid #' + r + g + b);
            });

        });

    </script>

</body>
</html>