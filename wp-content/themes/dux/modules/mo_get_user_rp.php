<?php

function mo_get_user_rp()
{
	$pid = _hui("user_rp");

	if (!$pid) {
		return false;
	}

	if (get_permalink($pid)) {
		return get_permalink($pid);
	}

	return false;
}


?>
