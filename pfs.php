<?php
/**
 * PFS root-level redirector for backwards compatibility
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2013
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

$_GET['e'] = 'pfs';

require 'index.php';
