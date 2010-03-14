<?php
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.auth.inc.php
Version=102
Updated=2006-apr-19
Type=Core
Author=Neocrome
Description=User authication
[END_SED]
==================== */

defined('SED_CODE') or die('Wrong URL');

$v = sed_import('v','G','PSW');

/* === Hook === */
$extp = sed_getextplugins('users.auth.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a=='check')
{
	sed_shield_protect();

	/* === Hook for the plugins === */
	$extp = sed_getextplugins('users.auth.check');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rusername = sed_import('rusername','P','TXT', 100, TRUE);
	$rpassword = sed_import('rpassword','P','PSW', 16, TRUE);
	$rcookiettl = sed_import('rcookiettl', 'P', 'INT');
	$rremember = sed_import('rremember', 'P', 'BOL');
	if(empty($rremember) && $rcookiettl > 0) $rremember = true;
	$rmdpass  = md5($rpassword);

	$login_param = preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $rusername) ?
		'user_email' : 'user_name';

	$sql = sed_sql_query("SELECT user_id, user_name, user_maingrp, user_banexpire, user_skin, user_theme, user_lang FROM $db_users WHERE user_password='$rmdpass' AND $login_param='".sed_sql_prep($rusername)."'");

	if ($row = sed_sql_fetcharray($sql))
	{
		$rusername = $row['user_name'];
		if ($row['user_maingrp']==-1)
		{
			sed_log("Log in attempt, user inactive : ".$rusername, 'usr');
			sed_redirect(sed_url('message', 'msg=152', '', true));
		}
		if ($row['user_maingrp']==2)
		{
			sed_log("Log in attempt, user inactive : ".$rusername, 'usr');
			sed_redirect(sed_url('message', 'msg=152', '', true));
		}
		elseif ($row['user_maingrp']==3)
		{
			if ($sys['now'] > $row['user_banexpire'] && $row['user_banexpire']>0)
			{
				$sql = sed_sql_query("UPDATE $db_users SET user_maingrp='4' WHERE user_id={$row['user_id']}");
			}
			else
			{
				sed_log("Log in attempt, user banned : ".$rusername, 'usr');
				sed_redirect(sed_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
			}
		}

		$ruserid = $row['user_id'];
		$rdefskin = $row['user_skin'];
		$rdeftheme = $row['user_theme'];

		$hashsalt = sed_unique(16);

		sed_sql_query("UPDATE $db_users SET user_lastip='{$usr['ip']}', user_lastlog = {$sys['now_offset']}, user_logcount = user_logcount + 1, user_hashsalt = '$hashsalt' WHERE user_id={$row['user_id']}");

		$passhash = md5($rmdpass.$hashsalt);
		$u = base64_encode($ruserid.':_:'.$passhash);

		if($rremember)
		{
			sed_setcookie($sys['site_id'], $u, time()+$cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
		}
		else
		{
			$_SESSION[$sys['site_id']] = $u;
		}

		$_SESSION['saltstamp'] = $sys['now_offset'];

		/* === Hook === */
		$extp = sed_getextplugins('users.auth.check.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql = sed_sql_query("DELETE FROM $db_online WHERE online_userid='-1' AND online_ip='".$usr['ip']."' LIMIT 1");
		sed_uriredir_apply($cfg['redirbkonlogin']);
		sed_uriredir_redirect(empty($redirect) ? sed_url('index') : base64_decode($redirect));
	}
	else
	{
		sed_shield_update(7, "Log in");
		sed_log("Log in failed, user : ".$rusername,'usr');
		sed_redirect(sed_url('message', 'msg=151', '', true));
	}
}

/* === Hook === */
$extp = sed_getextplugins('users.auth.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = $L['aut_logintitle'];
$out['head'] .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('users.auth'));

if ($cfg['maintenance'])
{
	$t->assign(array("USERS_AUTH_MAINTENANCERES" => $cfg['maintenancereason']));
	$t->parse("MAIN.USERS_AUTH_MAINTENANCE");
}

$t->assign(array(
	"USERS_AUTH_TITLE" => $L['aut_logintitle'],
	"USERS_AUTH_SEND" => sed_url('users', 'm=auth&a=check' . (empty($redirect) ? '' : "&redirect=$redirect")),
	"USERS_AUTH_USER" => "<input type=\"text\" class=\"text\" name=\"rusername\" size=\"16\" maxlength=\"32\" />",
	"USERS_AUTH_PASSWORD" => "<input type=\"password\" class=\"password\" name=\"rpassword\" size=\"16\" maxlength=\"32\" />",
	"USERS_AUTH_REGISTER" => sed_url('users', 'm=register')
));

/* === Hook === */
$extp = sed_getextplugins('users.auth.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';
?>