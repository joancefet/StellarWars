{include file="overall_header.tpl"}
{include file="left_menu.tpl"}
{include file="overall_topnav.tpl"}
<div id="content">
	<table style="width:50%">
		<tr>
			<th colspan="6">{$af_header}</td>
		</tr>
		<tr>
			<th style="width:5%"></th>
			<th style="width:10%">{$af_ticket_id}</th>
			<th style="width:35%">{$af_subject}</th>
			<th style="width:15%">{$af_status}</th>
			<th style="width:10%">Ultimo Editor</th>
			<th style="width:25%">{$af_ticket_posted}</th>
		</tr>
		{foreach key=TicketID item=TicketInfo from=$TicketsList}	
		<tr>
		<td>{if $TicketInfo.af_lastpostaction == 1}<img src="./styles/images/newaforum.png">{/if}</td>
		<td>{$TicketInfo.af_player_post}</td>
		<td><a href="#" onclick="ShowTicket('ticket_{$TicketID}');">{$TicketInfo.af_subject}</a></td>
		<td>{if $TicketInfo.af_status == 0}<span style="color:red">{$af_close}</span>{elseif $TicketInfo.af_status == 1}<span style="color:green">{$af_open}</span>{elseif $TicketInfo.af_status == 2}<span style="color:orange">{$af_admin_answer}</span>{elseif $TicketInfo.af_status == 3}<span style="color:green">{$af_player_answer}</span>{/if}{if $TicketInfo.af_status == 1}{if $TicketInfo.af_user_id == $TicketInfo.af_player_id}<a href="game.php?page=aforum&amp;action=close&amp;id={$TicketID}"> Trancar</a>{/if}{elseif $TicketInfo.af_status == 0}{if $TicketInfo.af_user_id == $TicketInfo.af_player_id}<a href="game.php?page=aforum&amp;action=open&amp;id={$TicketID}"> Destrancar</a>{/if}{/if}{if $TicketInfo.af_user_id == $TicketInfo.af_player_id}<a href="game.php?page=aforum&amp;action=delete&amp;id={$TicketID}" onclick="return confirm('{$af_delete_confirm}');"> Deletar</a>{/if}</td>
		<td>{$TicketInfo.af_lastpostplayer}</td>
		<td>{$TicketInfo.af_date}</td>
		</tr>
		{/foreach}
	</table>
	{foreach key=TicketID item=TicketInfo from=$TicketsList}
	<div id="ticket_{$TicketID}" style="display:none;" class="tickets">
		<form action="game.php?page=aforum&amp;action=send&amp;id={$TicketID}" method="POST">
			<table style="width:50%">
				
				<tr>
					<th>{$af_text}</th>
				</tr>
				
				<tr>
					<td>{$TicketInfo.af_text}</td>
				</tr>
				
				{if $TicketInfo.af_status == 0}<tr><th>{$af_ticket_close}</th></tr>{/if}
				<tr>
					<td>
					{if $TicketInfo.af_status != 0}
					<textarea cols="50" rows="10" name="text"></textarea><br><input type="submit" value="{$af_send}">
					{/if}
					</td>
				</tr>
			</table>
		</form>
	</div>
	{/foreach}
	<div id="newbutton" style="display:block;">
		<table style="width:50%">
			<tr>
				<th><a href="#" onclick="ShowTicket(0);">{$af_ticket_new}</a></th>
			</tr>
		</table>
	</div>
	<div id="new" style="display:none;">
		<form action="game.php?page=aforum&amp;action=newticket" method="POST">
			<table style="width:50%">
				<tr>
					<th colspan="2" width="50%">{$af_ticket_new}</th>
				</tr>
				<tr>
					<td>{$af_subject}:</td>
					<td><input type="text" name="subject"></td>
				</tr>
				<tr>
					<td colspan="2">{$af_input_text}</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea name="text" cols="50" rows="10"></textarea>
						<input type="submit" value="{$af_send}">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
{include file="planet_menu.tpl"}
{include file="overall_footer.tpl"}
