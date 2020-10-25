<?php
// $Id: pollresults.php,v 1.9 2005/03/15 00:19:15 w4z004 Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System //
// Copyright (c) 2000 xoopscube.org //
// <http://xoopscube.org> //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify //
// it under the terms of the GNU General Public License as published by //
// the Free Software Foundation; either version 2 of the License, or //
// (at your option) any later version.  //
//   //
// You may not change or alter any portion of this comment or credits //
// of supporting developers from this source code or any supporting //
// source code which is considered copyrighted (c) material of the //
// original comment or credit authors.  //
//   //
// This program is distributed in the hope that it will be useful, //
// but WITHOUT ANY WARRANTY; without even the implied warranty of //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the //
// GNU General Public License for more details. //
//   //
// You should have received a copy of the GNU General Public License //
// along with this program; if not, write to the Free Software //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA //
// ------------------------------------------------------------------------ //
include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/modules/votos/include/constants.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votos.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votosoption.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votoslog.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votosrenderer.php';
$poll_id = $_GET['poll_id'];
$poll_id = (!empty($poll_id)) ? (int)$poll_id : 0;
if (empty($poll_id)) {
    redirect_header('index.php', 0);

    exit();
}
$GLOBALS['xoopsOption']['template_main'] = 'votos_results.html';
require XOOPS_ROOT_PATH . '/header.php';
$poll = new XoopsPoll($poll_id);
$renderer = new XoopsPollRenderer($poll);
$renderer->assignResults($xoopsTpl);
require XOOPS_ROOT_PATH . '/include/comment_view.php';
require XOOPS_ROOT_PATH . '/footer.php';
