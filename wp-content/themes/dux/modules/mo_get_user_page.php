<?php

function mo_get_user_page()
{
	$pid = _hui("user_page");

	if (!$pid) {
		return false;
	}

	if (get_permalink($pid)) {
		return get_permalink($pid);
	}

	return false;
}


?>
