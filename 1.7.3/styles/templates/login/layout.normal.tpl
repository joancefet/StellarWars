{include file="main.header.tpl"}
{include file="main.navigation.tpl"}
<div id="content">
<div id="main">
<header>
<div id="logo">
<div id="logo_text">
<img src="login/images/game.png" />
</div>
</div>
<nav>
<div id="menu_container">
<ul class="sf-menu" id="nav">
<li><a href="index.php">{$LNG.menu_index}</a></li>
<li><a href="index.php?page=board" target="board">{$LNG.forum}</a></li>
<li><a href="index.php?page=news">{$LNG.menu_news}</a></li>
<li><a href="index.php?page=rules">{$LNG.menu_rules}</a></li>
<li><a href="index.php?page=battleHall">{$LNG.menu_battlehall}</a></li>
<li><a href="index.php?page=banList">{$LNG.menu_banlist}</a></li>
<li><a href="index.php?page=disclamer">{$LNG.menu_disclamer}</a></li>
</ul>
</div>
</nav>
</header>
<div id="site_content">
<div id="sidebar_container">
<div class="sidebar">
<a href="index.php?page=register"><input class="main-form2" type="submit" value="Register"></a><br>
<a href="index.php?page=lostPassword"><input class="main-form2" type="submit" value="Lost Password?"></a><br>
<h4>News</h4>
<p>
No News
</p>
</div>

</div>
<div class="content">
<p>
{block name=content} {/block}
</p>

</div>
</div>
<footer>
<p>Copyright &copy; {$gameName} and <a href="http://qwatakayean.co.vu">QwataKayean</a></p>
</footer>
</div>
<p>&nbsp;</p>
<!-- javascript at the bottom for fast page loading -->
<script type="text/javascript" src="login/js/jquery.js"></script>
<script type="text/javascript" src="login/js/jquery.easing-sooper.js"></script>
<script type="text/javascript" src="login/js/jquery.sooperfish.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$('ul.sf-menu').sooperfish();
});
</script>

{include file="main.footer.tpl" nocache}