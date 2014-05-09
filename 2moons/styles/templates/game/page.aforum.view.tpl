{block name="title" prepend}{$LNG.af_read} - {$LNG.lm_support}{/block}
{block name="content"}
<form action="game.php?page=aforum&mode=send" method="post" id="form">
<input type="hidden" name="id" value="{$ticketID}">
<table class="table519">
	{foreach $answerList as $answerID => $answerRow}	
	{if $answerRow@first}
	<tr>
		<th colspan="2">{$LNG.af_read} :: {$answerRow.subject}</th>
	</tr>
	{/if}
	<tr>
		<td class="left" colspan="2">
			{$LNG.af_msgtime} <b>{$answerRow.time}</b> {$LNG.af_from} <b>{$answerRow.ownerName}</b>
			{if $answerRow@first}
				<br>{$LNG.af_category}: {$categoryList[$answerRow.categoryID]}
			{/if}
			</p>
			<hr>
			<p>
				{$answerRow.message}
			</p>
		</td>
	</tr>
	{/foreach}
	{if $status < 2}
	<tr>
		<th colspan="2">{$LNG.af_answer}</th>
	</tr>
	<tr>
		<td style="width:30%"><label for="message">{$LNG.af_message}</label></td>
		<td style="width:70%"><textarea class="validate[required]" id="message" name="message" row="60" cols="8" style="height:100px;"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{$LNG.af_submit}"></td>
	</tr>
	{/if}
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