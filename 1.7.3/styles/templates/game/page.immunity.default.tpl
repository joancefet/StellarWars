{block name="title" prepend}Immunity{/block}
{block name="content"}
<table style="width:590px;">

<tbody><tr>
        <td colspan="2">
           {$p_state}{if !empty($immunity_active)} <b><span style="color:red;" class="countdown2"  secs="{$immunity_active}"></span></b>{/if}
       </td>
    </tr>
    <tr>
        <td>
            <img src="styles/theme/gow/images/immunity_page.png">
        </td>
        <td>
         This mod protect all your planets from attacks while your resource production stay same like before.<br>
Only fleet movement allowed is within your own planets <br>
Immunity is active 3 day and after immunity has a cooldown of 7 days that means you can reactivate it after 7 days.<br>
        Cost of Immunity Activation : 30.000 DM<br>
        Cost to end Immunity : 20.000 DM
        </td>
    </tr>
    
   <form action="game.php?page=immunity" method="POST">
   <input type="hidden" name="con" value="buy">
					
					<tr>
        <td colspan="2">
    {$next_immunity}{if !empty($immunity_next_active)} <span  class="countdown2"  secs="{$immunity_next_active}"></span>{/if}
    
				</form>
    
                    </td>
    </tr>
        </form>

       <form action="game.php?page=immunity" method="POST">
	   <input type="hidden" name="con" value="end">

  <tr>
        <td colspan="2">  
    {$end_immunity}
    
    </td>
    </tr>
    </form>
</tbody>
</table>
{/block}
