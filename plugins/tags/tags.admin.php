<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=admin
File=tags.admin
Hooks=admin.page.loop
Tags=admin.page.inc.tpl:{ADMIN_TAGS_ROW_TAG},{ADMIN_TAGS_ROW_URL}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Shows tags in page administration area
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Dayver - Pavel Tkachenko
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'])
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags', 'plug');
	$item_id = $row['page_id'];
	$tags = sed_tag_list($item_id);
	if (count($tags) > 0)
	{
		$tag_i = 0;
		foreach ($tags as $tag)
		{
			$tag_u = sed_urlencode($tag, $cfg['plugin']['tags']['translit']);
			$tl = ($lang != 'en' && $tag_u != urlencode($tag)) ? '&tl=1' : '';
			$t->assign(array(
				'ADMIN_TAGS_ROW_TAG' => $cfg['plugin']['tags']['title'] ? htmlspecialchars(sed_tag_title($tag)) : htmlspecialchars($tag),
				'ADMIN_TAGS_ROW_URL' => sed_url('plug', 'e=tags&a=pages'.$tl.'&t='.$tag_u)
			));
			$t->parse('PAGE.PAGE_ROW.ADMIN_TAGS_ROW');
			$tag_i++;
		}
	}
	else
	{
		$t->parse('PAGE.PAGE_ROW.ADMIN_NO_TAGS');
	}
}

?>