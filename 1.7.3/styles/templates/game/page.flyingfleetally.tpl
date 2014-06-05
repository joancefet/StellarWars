{block name="title" prepend}{$LNG.ff_name}{/block}
{block name="content"}
<table width="90%">
<tr>
	<th>{$LNG.ff_startuser}</th>
	<th>{$LNG.ff_startplanet}</th>
	<th>{$LNG.ff_targetuser}</th>
	<th>{$LNG.ff_targetplanet}</th>
	<th>{$LNG.ff_arrivaltime}</th>
</tr>
{foreach $FleetList as $FleetRow}
<tr>
	<td>{$FleetRow.startUserName}</td>
	<td>{$FleetRow.startPlanetName}&nbsp;[{$FleetRow.startPlanetGalaxy}:{$FleetRow.startPlanetSystem}:{$FleetRow.startPlanetPlanet}]</td>
	<td>{if $FleetRow.targetUserID != 0}{$FleetRow.targetUserName}{/if}</td>
	<td>{$FleetRow.targetPlanetName}&nbsp;[{$FleetRow.targetPlanetGalaxy}:{$FleetRow.targetPlanetSystem}:{$FleetRow.targetPlanetPlanet}]{if $FleetRow.targetPlanetID != 0}{/if}</td>
	<td>{if $FleetRow.state == 0}<span style="color:lime;">{/if}{$FleetRow.arrivaltime}{if $FleetRow.state == 0}</span>{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="12">{$LNG.ff_no_fleets}</td>
</tr>
{/foreach}
</table>

<table width="90%">
<tr>
	<th>{$LNG.ff_startuser}</th>
	<th>{$LNG.ff_startplanet}</th>
	<th>{$LNG.ff_targetuser}</th>
	<th>{$LNG.ff_targetplanet}</th>
	<th>{$LNG.ff_arrivaltime}</th>
</tr>
{foreach $FleetList2 as $FleetRow}
<tr>
	<td>{$FleetRow.startUserName}</td>
	<td>{$FleetRow.startPlanetName}&nbsp;[{$FleetRow.startPlanetGalaxy}:{$FleetRow.startPlanetSystem}:{$FleetRow.startPlanetPlanet}]</td>
	<td>{if $FleetRow.targetUserID != 0}{$FleetRow.targetUserName}{/if}</td>
	<td>{$FleetRow.targetPlanetName}&nbsp;[{$FleetRow.targetPlanetGalaxy}:{$FleetRow.targetPlanetSystem}:{$FleetRow.targetPlanetPlanet}]{if $FleetRow.targetPlanetID != 0}{/if}</td>
	<td>{if $FleetRow.state == 0}<span style="color:lime;">{/if}{$FleetRow.arrivaltime}{if $FleetRow.state == 0}</span>{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="12">{$LNG.ff_no_fleets}</td>
</tr>
{/foreach}
</table>
{/block}