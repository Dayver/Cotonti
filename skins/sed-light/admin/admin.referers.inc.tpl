<!-- BEGIN: REFERERS -->
	<div id="ajax_tab">
		<h2>{PHP.L.Referers}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_REFERERS_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
<!-- IF {PHP.usr.isadmin} -->
			<ul class="follow">
				<li><a href="{ADMIN_REFERERS_URL_PRUNE}">{PHP.L.adm_purgeall}</a></li>
				<li><a href="{ADMIN_REFERERS_URL_PRUNELOWHITS}">{PHP.L.adm_ref_lowhits}</a></li>
			</ul>
<!-- ENDIF -->
<!-- IF {PHP.is_ref_empty} -->
			<table class="cells">
				<tr>
					<td class="coltop" style="width:70%;">{PHP.L.Referer}</td>
					<td class="coltop" style="width:30%;">{PHP.L.Hits}</td>
				</tr>
<!-- ENDIF -->
<!-- BEGIN: REFERERS_ROW -->
			<tr>
				<td colspan="2"><a href="http://{ADMIN_REFERERS_REFERER}">{ADMIN_REFERERS_REFERER}</a></td>
			</tr>
<!-- BEGIN: REFERERS_URI -->
			<tr>
				<td>&nbsp; &nbsp; <a href="{ADMIN_REFERERS_URI}">{ADMIN_REFERERS_URI}</a></td>
				<td class="textright">{ADMIN_REFERERS_COUNT}</td>
			</tr>
<!-- END: REFERERS_URI -->
<!-- END: REFERERS_ROW -->
<!-- IF {PHP.is_ref_empty} -->
			</table>
			<p class="paging">{ADMIN_REFERERS_PAGINATION_PREV}{ADMIN_REFERERS_PAGNAV}{ADMIN_REFERERS_PAGINATION_NEXT} <span class="a1">{PHP.L.Total} : {ADMIN_REFERERS_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_REFERERS_ON_PAGE}</span></p>
<!-- ELSE -->
			<table class="cells">
				<tr>
					<td class="coltop" style="width:70%;">{PHP.L.Referer}</td>
					<td class="coltop" style="width:30%;">{PHP.L.Hits}</td>
				</tr>
				<tr>
					<td class="textcenter" colspan="2">{PHP.L.None}</td>
				</tr>
			</table>
<!-- ENDIF -->
	</div>
<!-- END: REFERERS -->