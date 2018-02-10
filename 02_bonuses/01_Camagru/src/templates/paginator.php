<?php

$i = 1;

if ($pages > 1)
{
	print("<div class=\"col-12 paginator row\">");
    if ($page != 1)
    {
        print("<a href=\"/gallery/1/\">&laquo;</a>");
        print("<a href=\"/gallery/".($page - 1)."/\">&lsaquo;</a>");
    }
	while ($i <= $pages) {
		if (
			($i > 0 && $i <= 3)
			|| ($i >= $page - 3 && $i <= $page + 2)
			|| ($i <= $pages &&  $i >= $pages - 3)
		)
		{
			if ($i == $page)
				print("<span disabled class=\"active\">".$i."</span>");
			else
				print("<a href=\"/gallery/".$i."/\">".$i."</a>");
		}
		else
		{
			print("<span disabled>&hellip;</span>");
			while (
				!(
					($i > 0 && $i <= 3)
					|| ($i >= $page - 3 && $i <= $page + 2)
					|| ($i <= $pages &&  $i >= $pages - 3)
				)
			)
			{
				$i++;
			}
		}
		$i++;
	}
    if ($page != $pages)
    {
        print("<a href=\"/gallery/".($page + 1)."/\">&rsaquo;</a>");
        print("<a href=\"/gallery/".$pages."/\">&raquo;</a>");
    }
	print("</div>");
}

?>
