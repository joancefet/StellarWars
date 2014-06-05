{block name="content"}
{foreach $newsList as $newsRow}
{if !$newsRow@first}<hr>{/if}
<font color="#F67F00"><b>{$newsRow.title}</b></font> &nbsp ({$newsRow.from})

<br><div><p>{$newsRow.text}</p></div>
{foreachelse}
<font color="#F67F00"><b>{$LNG.news_does_not_exist}</b></font></h1>
{/foreach}
{/block}