<?php

function mo_comments_list($comment, $args, $depth)
{
	$GLOBALS["comment"] = $comment;
	global $commentcount;
	global $wpdb;
	global $post;

	if (!$commentcount) {
		$comments = $wpdb->get_results("SELECT * FROM " . $wpdb->comments . " WHERE comment_post_ID = " . $post->ID . " AND comment_type = '' AND comment_approved = '1' AND !comment_parent");
		$cnt = count($comments);
		$page = get_query_var("cpage");
		$cpp = get_option("comments_per_page");
		if ((ceil($cnt / $cpp) == 1) || ((1 < $page) && ($page == ceil($cnt / $cpp)))) {
			$commentcount = $cnt + 1;
		}
		else {
			$commentcount = ($cpp * $page) + 1;
		}
	}

	echo "<li ";
	comment_class();
	echo " id=\"comment-" . get_comment_id() . "\">";

	if (!$parent_id = $comment->comment_parent) {
		echo "<span class=\"comt-f\">";
		printf("#%1\$s", --$commentcount);
		echo "</span>";
	}

	echo "<div class=\"comt-avatar\">";
	echo _get_the_avatar($user_id = $comment->user_id, $user_email = $comment->comment_author_email);
	echo "</div>";
	echo "<div class=\"comt-main\" id=\"div-comment-" . get_comment_id() . "\">";
	comment_text();

	if ($comment->comment_approved == "0") {
		echo "<span class=\"comt-approved\">待审核</span>";
	}

	echo "<div class=\"comt-meta\"><span class=\"comt-author\">" . get_comment_author_link() . "</span>";
	echo _get_time_ago($comment->comment_date);

	if ($comment->comment_approved !== "0") {
		$replyText = get_comment_reply_link(array_merge($args, array("add_below" => "div-comment", "depth" => $depth, "max_depth" => $args["max_depth"])));
		echo preg_replace("# href=[\s\S]*? onclick=#", " href=\"javascript:;\" onclick=", $replyText);
	}

	echo "</div>";
	echo "</div>";
}


?>
