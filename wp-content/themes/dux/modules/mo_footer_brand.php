<?php

echo "<div class=\"branding branding-black\">\r\n\t<div class=\"container\">\r\n\t\t<h2>";
echo _hui("footer_brand_title");
echo "</h2>\r\n\t\t";

for ($i = 1; $i <= 2; $i++) {
	if (_hui("footer_brand_btn_text_" . $i) && _hui("footer_brand_btn_href_" . $i)) {
		echo "<a" . (_hui("footer_brand_btn_blank_" . $i) ? " target=\"blank\"" : "") . " class=\"btn btn-lg\" href=\"" . _hui("footer_brand_btn_href_" . $i) . "\">" . _hui("footer_brand_btn_text_" . $i) . "</a>";
	}
}

echo "\t</div>\r\n</div>";

?>
