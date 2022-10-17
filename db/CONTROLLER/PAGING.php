<?php
function paging($qry, $per_page, $num, $mpage, $fieldname, $countPr){
    include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
    $pages = ceil($countPr/$per_page);
    $page = $mpage;
    echo '<a href="?'.$fieldname.'='.'1">First</a> ';
    if($pages != 1 && $pages != 0)
    {	
        $prev = $page - 1;
        if($prev != 0)
        {
            echo '<a href="?'.$fieldname.'='.$prev.'">Previous</a> ';
	}
        else
        {
            echo '<a href="#">Previous</a> ';
	}
	
        if($page >= $num)
        {
            if($pages < $page+2)
            {
                $tgh = $pages-4;
            }
            else
            {
		$tgh = $page-2;
            }
	}
        else
        {
            $tgh = 1;
	}
		
	$y=0;
	for($x = $tgh; $x <= $pages; $x++)
	{
            $y++;
            if($y<=5)
            {
                if($page==$x)
                {
                    echo "<a href='?".$fieldname."=".$x. "' class=\"current\" >".$x."</a> ";
		}
                else
                {
                    echo "<a href='?".$fieldname."=".$x. "'>".$x."</a> ";
                }
            }
            else
            {
                break;	
            }
	}  //for
	
	
	
	if($pages>$page)
        {
            $next = $page + 1;
            echo '<a href="?'.$fieldname.'='.$next.'">Next</a> ';
        }
        else
        {
            echo '<a href="#">Next</a>';
	}
	echo '<a href=?'.$fieldname.'='.$pages.'>Last</a> ';
	$mulai = (($page-1) * $per_page) + 1;
        $akhir = $page*$per_page;
        if($akhir > $countPr)$akhir = $countPr;
	echo "  |<span class=\"arial11black\">  View   : </span>";
	echo "  <span class=\"arial11blue\">"."  ".$mulai. " - ". $akhir."  "."</span>";
	echo "  <span class=\"arial11black\">". "  of  " . "</span>";
	echo "  <span class=\"arial11blue\">". $countPr."</span>";   
			
    }
			
}
?>
