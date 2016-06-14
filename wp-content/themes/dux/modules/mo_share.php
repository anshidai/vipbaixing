<?php

function mo_share($stop = "")
{
	$shares = array("qzone", "tsina", "weixin", "tqq", "sqq", "bdhome", "tqf", "renren", "diandian", "youdao", "ty", "kaixin001", "taobao", "douban", "fbook", "twi", "mail", "copy");
	$html = "";

	foreach ($shares as $value ) {
		$html .= "<a class=\"bds_" . $value . "\" data-cmd=\"" . $value . "\"></a>";

		if ($stop == $value) {
			break;
		}
	}

	echo __("分享到：", "haoui") . $html;

	if (!$stop) {
		echo "<a class=\"bds_more\" data-cmd=\"more\">" . __("更多", "haoui") . "</a> (<a class=\"bds_count\" data-cmd=\"count\"></a>)";
	}
}


?>
