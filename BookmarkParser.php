<?php 
////////////////////////////////////////////////////////////////
/*
This class parses IE, Netscape and Opera bookmark files and returns
arrays with the bookmark / folder information.


For the lastest version go to:
http://www.phpclasses.org/browse.html/package/803.html

FUNCTIONS:
    function parseOpera($url, $folderID, $urlFunction, $folderFunction)
    function parseNetscape($url, $folderID, $urlFunction, $folderFunction) {
    function parseInternetExplorer($url, $folderID, $urlFunction, $folderFunction, $firstCall = true)

Those two function must be passed on to the three functions above:

    urlFunction($bookmark, $depth, $no)
       $bookmark        --> an associative array 
       $depth               the folders depth
       $no                  the current bookmark count
    
    folderFunction($folder, $depth, $no)
       $folder          --> an associative array 
       $depth               the folders depth
       $no                  the current folder count

////////////////////////////////////////////////////////////////

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.
    
    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.
    
    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
////////////////////////////////////////////////////////////////
/**
* This class parses IE, Netscape and Opera bookmark files and returns arrays with the bookmark / folder information.
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
* @version 	    0.61 - 2003/07/07

<p>
History / Changes<br>
<table border="1" width="100%" cellpadding="3"><tr>
      <th>Version</th>    <th>Reported By</th>        <th>File / Function Changed</th>    <th>Date of Change</th> <th>Commment</th>
</tr><tr>
      <td>0.61</td>        <td>J.  Jongsma</td>        <td>regular expressions</td>            <td>2003/07/07</td>     <td>change in parseOpera()</td>
</tr><tr>
      <td>0.6</td>        <td>K.Kamperman</td>        <td>regular expressions</td>            <td>2002/11/18</td>     <td>6 times faster parsing Netscape bookmarks</td>
</tr><tr>
      <td>06</td>        <td>-</td>        <td>_callFunction()</td>            <td>2002/11/18</td>     <td>added function to call user functions</td>
</tr></table>
 
* @access   public
*/

class BookmarkParser {

/**
* The generated error messages, line feed seperated
* 
* @access   public
* @type     String
*/
var $error_message = '';

/**
* The number of folders parsed after a function call
* 
* @access   public
* @type     Integer
*/
var $foldersParsed = 0;

/**
* The number of bookmarks parsed after a function call
* 
* @access   public
* @type     Integer
*/
var $urlsParsed = 0;

////////////////////////////////////////////////////////////////
/**
* Parses an Opera bookmark file
*
* Parses the file, default name for bookmark file is "Opera6.adr"
* Tested with Opera 6.
*
* @access   public
*
* @param    String      $url   url to the bookmark file
* @param    int         $folderID  id of the root folder
* @param    String    $urlFunction    the function name to be called when an url is parsed
* @param    String    $folderFunction the function name to be called when a bookmark is parsed
*
* @return   int         -1 if error occurs
*/

function parseOpera($url, $folderID, $urlFunction, $folderFunction) {
	$this->foldersParsed = 0;
	$this->urlsParsed = 0;
	$depth = 0;
	$parents = array();
	array_push($parents, $folderID);
	// is it a file?
	if (is_file($url)) {
		// open	file
		$fp	= @fopen($url, "r-");
		if (@$fp) {
    		// is it an	opara bookmark file?
    		$line =	str_replace("\n", "", fgets($fp, 4096));
    		if (preg_match('/Opera Hotlist version 2.0/', $line))	{
        		// insert Opera	root in	DB
        		// read	lines
        		while (!@feof ($fp)) {
            		$line =	str_replace("\n", "", fgets($fp, 4096));
        			// folder found
        			if (preg_match('/^[\s]*#folder/i', $line)) {
                		// extract the name
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
                        $name = $tmp[1];
                		// extract create creation date
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
                        $created = $tmp[1];
                		// extract the visit date
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
                        $visited = $tmp[1];
                        // insert into db                    
                        $this->foldersParsed++;
                        $this->_callFunction($folderFunction, false, array(  "name" => $name,
                                                "descr" => "",
                                                "created" => $created,
                                                "parent" => $parents[$depth],
                                                "added" => $created,
                                                "visited" =>  $visited)
                                         ,$depth, $this->foldersParsed);

                        // current id of folder is stored in a stack
        				array_push($parents, $folderID + $this->foldersParsed);
                        $depth++;
        			}
        			// bookmark	found
        			else if (preg_match('/^#url/i', $line)) {
        				// extract url 
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
        				$descr = $tmp[1];
                		// extract the name
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
        				$url = $tmp[1];
                		// extract create creation date
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
                        $created = $tmp[1];
                		// extract the visit date
                		$line =	str_replace("\n", "", fgets($fp, 4096));
                        $tmp = explode("=", $line, 2);
                        $visited = $tmp[1];
                        // insert into db
                        $this->urlsParsed++;
                        $this->_callFunction($urlFunction, false, array( "url"       => $url,
                                            "descr"     => $descr,
                                            "parent"    => $parents[$depth],
                                            "added"     => $created,
                                            "visited"  =>  $visited)
                                    ,$depth, $this->urlsParsed);
        			}
        			// folder closed
        			else if    (preg_match('/^[\s]*-/', $line))    {
        				array_pop($parents);
        				$depth--;
        			}
        		}
        		fclose($fp);
        		return true;
        	} else {
            	$this->error_message .= "parseOpera():	Wrong header!<br>\n";
            	return -1;
    		}
	    } else {
        	$this->error_message .= "parseOpera(): File error!<br>\n";
        	return -1;
		}
	        
	} else {
    	$this->error_message .= "parseOpera(): Wrong header!<br>\n";
    	return -1;
    }
}

////////////////////////////////////////////////////////////////
/**
* Parses a Netscape bookmark file
*
* Parses the file, default name is "bookmarks.html".
* Tested with Netscape 4.x and 6.x.
*
* @access   public
*
* @param    String      $url   url to the bookmark file
* @param    int         $folderID  id of the root folder
* @param    String    $urlFunction    the function name to be called when an url is parsed
* @param    String    $folderFunction the function name to be called when a bookmark is parsed
*
* @return   int         -1 if error occurs
*/

function parseNetscape($url, $folderID, $urlFunction, $folderFunction) {
	$this->foldersParsed = 0;
	$this->urlsParsed = 0;
	$depth = 0;
	$parents = array();
	array_push($parents, $folderID);
	// is it a file?
	if (is_file($url)) {
		// open	file
		$fp	= @fopen($url, "r-");
    }
    if (@$fp) {
		// is it a ns bookmark file?
        $line =	str_replace("\n", "", fgets($fp, 4096));
		if (!preg_match('/<!DOCTYPE NETSCAPE-Bookmark-file-1>/i', $line)) {
			$this->error_message .= "parseNetscape(): Wrong header!\n";
			return -1;
		}
		// insert NS root in DB
		// read	lines
		while (!@feof ($fp)) {
            $line =	str_replace("\n", "", fgets($fp, 54096));
			// extract add_date
			preg_match("(/ADD_DATE=\"([^\"]*/i))", $line, $match);
			@$added = $match[1];
			// folder found
			if (preg_match('/<H3[^>]*>(.*)<\/H3>/i',	$line, $match))	{
                $name = $match[1];
                $this->foldersParsed++;
                $this->_callFunction($folderFunction, false, array( "name"       => $name,
                                        "parent"    => $parents[$depth],
                                        "added"     => $added)
                                ,$depth, $folderID + $this->foldersParsed);
				array_push($parents, $folderID + $this->foldersParsed);
				$depth++;
			}
			// bookmark	found
			else if	(preg_match('/<A HREF="([^"]*)[^>]*>(.*)<\/A>/i', $line,	$match)) {
				// extract url and descr
				$url = $match[1];
				$descr = $match[2];
				// extract dates
				preg_match("/ADD_DATE=\"([^\"]*)/i", $line, $match);
				@$add_date = $match[1];
				preg_match("/LAST_VISIT=\"([^\"]*)/i", $line, $match);
				@$visited = $match[1];
                preg_match("/LAST_MODIFIED=\"([^\"]*)/i", $line, $match);
				@$modified = $match[1];
				preg_match("/ICON=\"([^\"]*)/i", $line, $match);
				@$icon = $match[1];
                
                // insert into db
                        $this->urlsParsed++;
                        $this->_callFunction($urlFunction, false, array( "url"      => $url,
                                            "descr"    => $descr,
                                            "parent"   => $parents[$depth],
                                            "added"    => $add_date,
                                            "modified" => $modified,
											"icon"	   => $icon,
                                            "visited"  => $visited)
                                    ,$depth, $this->urlsParsed);
        			}
			// folder closed
			else if	(preg_match('/<\/DL>/i', $line))	{
				array_pop($parents);
				$depth--;
			}
		}
		fclose($fp);
	} else {
			$this->error_message .= "parseNetscape(): file error!\n";
			return -1;
    }
}

////////////////////////////////////////////////////////////////
/**
* Parses an IE bookmarks folder.
*
* Parses the IE folder and files.
*
* @access   public
*
* @param    String      $url   url to the bookmark file
* @param    int         $folderID  id of the root folder
* @param    String    $urlFunction    the function name to be called when an url is parsed
* @param    String    $folderFunction the function name to be called when a bookmark is parsed
* @param    boolean     $firstCall  only true, upon the first call
*
* @return   int         -1 if error occurs
*/

function parseInternetExplorer($url, $folderID, $urlFunction, $folderFunction, $firstCall = true) {
	if ($firstCall) {
    	$this->foldersParsed = 0;
	    $this->urlsParsed = 0;
	}
	static $depth = 0;
	// open	directory
	$d = @dir($url);
	while($entry = $d->read()) {
		// is not .	or ..
		if ($entry != "." && $entry	!= "..") {
			// is it a dir?
			if (is_dir("$url/$entry")) {
			    $depth++;
                $this->_callFunction($folderFunction, false,
                                    array( "name"      => $entry,
                                            "descr"     => "",
                                            "parent"    => $folderID), $depth, $this->foldersParsed + $depth);
				// visit it
				$this->parseInternetExplorer("$url/$entry", $folderID + 1, $urlFunction, $folderFunction, false);
                $this->foldersParsed++;
                $depth--;
			// is there	a ie internet shortcut?
			} else if (preg_match("/.url$/i", $entry)) {
            	$modified =	"";
            	$lineno =	0;
            	// open	it
            	$fp	= @fopen("$url/$entry", "r-");
            	if (@$fp) {
            		$name =	substr(basename($entry), 0, strlen(basename($entry)) - 4);
            		while (!@feof ($fp)) {
            			$lineno++;
                        $line =	str_replace("\n", "", @fgets($fp, 4096));
            			// extract url
            			if (preg_match("/^url=/i", $line)) {
            				$href = trim(substr($line, 4));
            			} elseif (preg_match("/^modified=/i", $line)) {
            				$modified =	trim(substr($line, 9));
            			}
            		}
                    // insert into db
                        $this->urlsParsed++;
                        $this->_callFunction($urlFunction, false, array( "url"      => $href,
                                            "descr"    => $name,
                                            "parent"   => $folderID + $this->foldersParsed)
                                    ,$depth, $this->urlsParsed);
            	} else {
            		$this->error_message .= "parseInternetExplorer(): file error: $url!<br>\n";
            		return -1;
            	}
            	fclose ($fp);

			}
		}
	}
	$d->close();
}

////////////////////////////////////////////////////////////////
// 10-22-02
/**
* Checks 
*
* @access   private
*
* @param    String  $functionName
* @param    boolean $abortAmbiguous  
* @param    mixed   the params for the function
*
* @return   Integer    -1 when an error occurs or the retrun value of the function
*/

function _callFunction($functionName, $abortAmbiguous = false) {
        // # of params
        $count = func_num_args();
        // array for passed on params
        $params = array();
        for ($i=2; $i < $count; $i++) {
            $params[$i-1] = func_get_arg($i);
        }
        // flags if funcrion exists in a class or outside
        if (is_array($functionName)) {
            return call_user_func_array($functionName, $params);
        }
        $isInside = method_exists(@$this, $functionName);
        $isOutside = function_exists($functionName);
        // do we need to abort if function name is ambigous?
        if ($abortAmbiguous) {
            if ($isInside && $isOutside) {
                return -1;
            }
        } 
        // call the inner method first
        if ($isInside) {
            return call_user_func_array(array($this, $functionName), $params);
        // or the "outer" one
        } else if ($isOutside) {
            return call_user_func_array($functionName, $params);
        // function does not exist at all
        } else if ($functionName) {
            return -1;
        }
}

////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////
?>
