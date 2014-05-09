{block name="title" prepend}{$LNG.af_create_head} - {$LNG.lm_support}{/block}
{block name="content"}
<form action="game.php?page=aforum&mode=send" method="post" id="form">
<input type="hidden" name="id" value="0">
<table style="width:760px;">
	<tr>
		<th colspan="2">{$LNG.af_create_head}</th>
	</tr>
	<tr>
		<td colspan="2" class="left">{$LNG.af_create_info}</td>
	</tr>
	<tr>
		<td style="width:30%"><label for="category">{$LNG.af_category}</label></td>
		<td style="width:70%"><select id="category" name="category">{html_options options=$categoryList}</select></td>
	</tr>
	<tr>
		<td><label for="subject">{$LNG.af_subject}</label></td>
		<td><input class="validate[required]" type="text" id="subject" name="subject" size="40" maxlength="255"></td>
	</tr>
	<tr>
		<td><label for="message">{$LNG.af_message}</label></td>
		<td><textarea class="validate[required]" id="message" name="message" row="60" cols="8" style="height:100px;"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{$LNG.af_submit}"></td>
	</tr>
</table>
</form>
{/block}
{block name="script" append}
<script>
$(document).ready(function() {
$("#form").validationEngine('attach');
});
</script>
{/block}