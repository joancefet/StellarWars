{block name="title" prepend}{$LNG.lm_support}{/block}
{block name="content"}
<table style="width:760px;">
	<tr>
		<th colspan="5">{$LNG.af_header}</th>
	</tr>
	<tr style="height:20px;">
		<td colspan="5"><a href="game.php?page=aforum&amp;mode=create">{$LNG.af_new}</a></td>
	</tr>
	<tr>
		<th style="width:10%">{$LNG.af_id}</td>
		<th style="width:45%">{$LNG.af_subject}</td>
		<th style="width:15%">{$LNG.af_answers}</td>
		<th style="width:15%">{$LNG.af_date}</td>
		<th style="width:15%">{$LNG.af_status}</td>
	</tr>
	{foreach $ticketList as $TicketID => $TicketInfo}	
	<tr>
		<td><a href="game.php?page=aforum&amp;mode=view&amp;id={$TicketID}">#{$TicketID}</a></td>
		<td><a href="game.php?page=aforum&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.subject}</a></td>
		<td>{$TicketInfo.answer - 1}</td>
		<td>{$TicketInfo.time}</td>
		<td>{if $TicketInfo.status == 0}<span style="color:green">{$LNG.af_status_open}</span>{elseif $TicketInfo.status == 1}<span style="color:orange">{$LNG.af_status_answer}</span>{else}<span style="color:red">{$LNG.af_status_closed}</span>{/if}</td>
	</tr>
	{/foreach}
</table>
{/block}