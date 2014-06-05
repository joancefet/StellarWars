{block name="content"} 
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
<h4>Game Rates</h4>
<p>
{$game_speed}x Game Speed<br>
{$fleet_speed}x Fleet Speed<br>
{$resource_multiplier}x Ressource Speed<br>
{$Fleet_Cdr}% Fleet to Debris<br>
{$Defs_Cdr}% Defense to Debris<br>
</p>
<p>
Players Online : <font color="#F67F00">{$online}</font><br>
Our newest Player: <font color="#F67F00">{$last}</font><br>
Amount of Users  : <font color="#F67F00">{$user}</font><br>
Amount of Planets: <font color="#F67F00">{$planet}</font><br>
</p>
</div>

</div>
<div class="content">
<p>
<form id="login" name="login" action="index.php?page=login" data-action="index.php?page=login" method="post">
<table><tr>
<td><input class="main-form" placeholder="Username" name="username" id="username" type="text"></td>
<td><input class="main-form" placeholder="Password" name="password" id="password" type="password"></td>
<td><input class="main-form2" type="submit" value="{$LNG.loginButton}"></td>
</tr></table>
</form>
</p>

<p>{$descText}</p>
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
{/block}
{block name="script" append}
<script>{if $code}alert({$code|json});{/if}</script>
{/block}