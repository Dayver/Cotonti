<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */
/**
 * Administration panel - Referers manager
 *
 * @package Referers
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'referers');
cot_block($usr['auth_read']);

$tt = new XTemplate(cot_tplfile('referers.admin', 'plug', true));

$db_referers = (isset($db_referers)) ? $db_referers : $db_x . 'referers';
require_once cot_langfile('referers', 'plug');
$adminhelp = $L['adm_help_referers'];
$adminsubtitle = $L['Referers'];

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

/* === Hook  === */
foreach (cot_getextplugins('referers.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'prune' && $usr['isadmin'])
{
	$db->query("TRUNCATE $db_referers") ? cot_message('adm_ref_prune') : cot_message('Error');
}
elseif($a == 'prunelowhits' && $usr['isadmin'])
{
	$db->delete($db_referers, 'ref_count < 6') ? cot_message('adm_ref_prunelowhits') : cot_message('Error');
}

$totalitems = $db->countRows($db_referers);
$pagenav = cot_pagenav('admin', 'm=other&p=referers', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = $db->query("SELECT * FROM $db_referers ORDER BY ref_count DESC LIMIT $d, ".$cfg['maxrowsperpage']);

if($sql->rowCount() > 0)
{
	while($row = $sql->fetch())
	{
		preg_match("#//([^/]+)/#", $row['ref_url'], $a);
		$host = preg_replace('#^www\.#i', '', $a[1]);
		$referers[$host][$row['ref_url']] = $row['ref_count'];
	}
	$sql->closeCursor();

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('referers.admin.loop');
	/* ===== */
	foreach($referers as $referer => $url)
	{

		$tt->assign('ADMIN_REFERERS_REFERER', htmlspecialchars($referer));

		foreach($url as $uri => $count)
		{
			$tt->assign(array(
				'ADMIN_REFERERS_URI' => htmlspecialchars(cot_cutstring($uri, 128)),
				'ADMIN_REFERERS_COUNT' => $count,
				'ADMIN_REFERERS_ODDEVEN' => cot_build_oddeven($ii)
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$tt->parse('MAIN.REFERERS_ROW.REFERERS_URI');
		}
		$tt->parse('MAIN.REFERERS_ROW');
		$ii++;
	}
	$is_ref_empty = true;
}
else
{
	$is_ref_empty = false;
}

$tt->assign(array(
	'ADMIN_REFERERS_URL_PRUNE' => cot_url('admin', 'm=other&p=referers&a=prune&'.cot_xg()),
	'ADMIN_REFERERS_URL_PRUNELOWHITS' => cot_url('admin', 'm=other&p=referers&a=prunelowhits&'.cot_xg()),
	'ADMIN_REFERERS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_REFERERS_PAGNAV' => $pagenav['main'],
	'ADMIN_REFERERS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_REFERERS_TOTALITEMS' => $totalitems,
	'ADMIN_REFERERS_ON_PAGE' => $ii
));

cot_display_messages($tt);

/* === Hook  === */
foreach (cot_getextplugins('referers.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$plugin_body = $tt->text('MAIN');
