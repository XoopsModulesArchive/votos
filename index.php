<?php
// $Id: index.php,v 1.9 2005/03/17 05:40:40 okazu Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System //
// Copyright (c) 2000 xoopscube.org //
// <http://xoopscube.org> //
// ------------------------------------------------------------------------- //
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
if (!empty($_POST['poll_id'])) {
    $poll_id = (int)$_POST['poll_id'];
} elseif (!empty($_GET['poll_id'])) {
    $poll_id = (int)$_GET['poll_id'];
}
if (empty($poll_id)) {
    $GLOBALS['xoopsOption']['template_main'] = 'votos_index.html';

    require XOOPS_ROOT_PATH . '/header.php';

    $limit = (!empty($_GET['limit'])) ? (int)$_GET['limit'] : 50;

    $start = (!empty($_GET['start'])) ? (int)$_GET['start'] : 0;

    $xoopsTpl->assign('lang_pollslist', _PL_POLLSLIST);

    $xoopsTpl->assign('lang_pollquestion', _PL_POLLQUESTION);

    $xoopsTpl->assign('lang_pollvoters', _PL_VOTERS);

    $xoopsTpl->assign('lang_votes', _PL_VOTES);

    $xoopsTpl->assign('lang_expiration', _PL_EXPIRATION);

    $xoopsTpl->assign('lang_results', _PL_RESULTS);

    // add 1 to $limit to know whether there are more polls

    $polls_arr = &XoopsPoll::getAll([], true, 'weight ASC, end_time DESC', $limit + 1, $start);

    $polls_count = count($polls_arr);

    $max = ($polls_count > $limit) ? $limit : $polls_count;

    for ($i = 0; $i < $max; $i++) {
        $polls = [];

        $polls['pollId'] = $polls_arr[$i]->getVar('poll_id');

        if ($polls_arr[$i]->getVar('end_time') > time()) {
            $polls['pollEnd'] = formatTimestamp($polls_arr[$i]->getVar('end_time'), 'm');

            $polls['pollQuestion'] = "<a href='index.php?poll_id=" . $polls_arr[$i]->getVar('poll_id') . "'>" . $polls_arr[$i]->getVar('question') . '</a>';
        } else {
            $polls['pollEnd'] = "<span style='color:#ff0000;'>" . _PL_EXPIRED . '</span>';

            $polls['pollQuestion'] = $polls_arr[$i]->getVar('question');
        }

        $polls['pollVoters'] = $polls_arr[$i]->getVar('voters');

        $polls['pollVotes'] = $polls_arr[$i]->getVar('votes');

        $xoopsTpl->append('polls', $polls);

        unset($polls);
    }

    require XOOPS_ROOT_PATH . '/footer.php';
} elseif (!empty($_POST['option_id'])) {
    $voted_polls = (!empty($HTTP_COOKIE_VARS['voted_polls'])) ? $HTTP_COOKIE_VARS['voted_polls'] : [];

    $mail_author = false;

    $poll = new XoopsPoll($poll_id);

    if (!$poll->hasExpired()) {
        if (empty($voted_polls[$poll_id])) {
            if ($xoopsUser) {
                if (XoopsPollLog::hasVoted($poll_id, xoops_getenv('REMOTE_ADDR'), $xoopsUser->getVar('uid'))) {
                    setcookie("voted_polls[$poll_id]", 1, 0);

                    $poll->vote($_POST['option_id']);

                    $poll->updateCount();

                    $msg = _PL_THANKSFORVOTE;
                } else {
                    $poll->vote($_POST['option_id'], xoops_getenv('REMOTE_ADDR'), $xoopsUser->getVar('uid'));

                    $poll->updateCount();

                    setcookie("voted_polls[$poll_id]", 1, 0);

                    $msg = _PL_THANKSFORVOTE;
                }
            } else {
                if (XoopsPollLog::hasVoted($poll_id, xoops_getenv('REMOTE_ADDR'))) {
                    setcookie("voted_polls[$poll_id]", 1, 0);

                    //add

                    $poll->vote($_POST['option_id']);

                    $poll->updateCount();

                    $msg = _PL_THANKSFORVOTE;
                } else {
                    $poll->vote($_POST['option_id'], xoops_getenv('REMOTE_ADDR'));

                    $poll->updateCount();

                    setcookie("voted_polls[$poll_id]", 1, 0);

                    $msg = _PL_THANKSFORVOTE;
                }
            }
        } else {
            $poll->vote($_POST['option_id'], xoops_getenv('REMOTE_ADDR'), $xoopsUser->getVar('uid'));

            $poll->updateCount();

            setcookie("voted_polls[$poll_id]", 1, 0);

            $msg = _PL_THANKSFORVOTE;
        }
    } else {
        $msg = _PL_SORRYEXPIRED;

        if (POLL_MAILED != $poll->getVar('mail_status')) {
            $xoopsMailer = getMailer();

            $xoopsMailer->useMail();

            $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/votos/language/' . $xoopsConfig['language'] . '/mail_template/');

            $xoopsMailer->setTemplate('mail_results.tpl');

            $author = new XoopsUser($poll->getVar('user_id'));

            $xoopsMailer->setToUsers($author);

            $xoopsMailer->assign('POLL_QUESTION', $poll->getVar('question'));

            $xoopsMailer->assign('POLL_START', formatTimestamp($poll->getVar('start_time'), 'l', $author->timezone()));

            $xoopsMailer->assign('POLL_END', formatTimestamp($poll->getVar('end_time'), 'l', $author->timezone()));

            $xoopsMailer->assign('POLL_VOTES', $poll->getVar('votes'));

            $xoopsMailer->assign('POLL_VOTERS', $poll->getVar('voters'));

            $xoopsMailer->assign('POLL_ID', $poll->getVar('poll_id'));

            $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);

            $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);

            $xoopsMailer->assign('SITEURL', $xoopsConfig['xoops_url'] . '/');

            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

            $xoopsMailer->setFromName($xoopsConfig['sitename']);

            $xoopsMailer->setSubject(sprintf(_PL_YOURPOLLAT, $author->uname(), $xoopsConfig['sitename']));

            if (false !== $xoopsMailer->send()) {
                $poll->setVar('mail_status', POLL_MAILED);

                $poll->store();
            }
        }
    }

    redirect_header(XOOPS_URL . "/modules/votos/pollresults.php?poll_id=$poll_id", 1, $msg);

    exit();
} elseif (!empty($poll_id)) {
    $GLOBALS['xoopsOption']['template_main'] = 'votos_view.html';

    require XOOPS_ROOT_PATH . '/header.php';

    $poll = new XoopsPoll($poll_id);

    $renderer = new XoopsPollRenderer($poll);

    $renderer->assignForm($xoopsTpl);

    $xoopsTpl->assign('lang_vote', _PL_VOTE);

    $xoopsTpl->assign('lang_results', _PL_RESULTS);

    require XOOPS_ROOT_PATH . '/footer.php';
}