</section>
<footer class="footer">
  <div class="footer-inner">
    <div class="copyright pull-left">
      <p>
        <?php if( dopt('d_footcode_b') ) echo dopt('d_footcode'); ?>&nbsp;&nbsp;
      </p>
    </div>
    <div class="trackcode pull-right">
      <span>Powered by <a href="http://www.vipbaixing.com">VIP百姓网</a> ·   <a  href="http://www.xuexicang.com" target="_blank">学习仓资源网</a></span>
      <?php if( dopt('d_track_b') ) echo dopt('d_track'); ?>
    </div>
  </div>
</footer>
<?php 
wp_footer(); 
global $dHasShare; 
if($dHasShare == true){ 
	echo'<script>with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>';
}
?>
</body></html>