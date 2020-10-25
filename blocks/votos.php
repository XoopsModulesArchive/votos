<?php
// $Id: votos.php,v 1.5 2005/02/12 11:39:12 okazu Exp $
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
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votos.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/class/votosoption.php';
require_once XOOPS_ROOT_PATH . '/modules/votos/language/' . $xoopsConfig['language'] . '/main.php';
function b_votos_show()
{
    $block = [];

    $polls = &XoopsPoll::getAll(['display=1'], true, 'weight ASC, end_time DESC');

    $count = count($polls);

    $block['lang_vote'] = _PL_VOTE;

    $block['lang_results'] = _PL_RESULTS;

    for ($i = 0; $i < $count; $i++) {
        $options_arr = &XoopsPollOption::getAllByPollId($polls[$i]->getVar('poll_id'));

        $option_type = 'radio';

        $option_name = 'option_id';

        if (1 == $polls[$i]->getVar('multiple')) {
            $option_type = 'checkbox';

            $option_name .= '[]';
        }

        foreach ($options_arr as $option) {
            $options[] = ['id' => $option->getVar('option_id'), 'text' => $option->getVar('option_text')];
        }

        $poll = ['id' => $polls[$i]->getVar('poll_id'), 'question' => $polls[$i]->getVar('question'), 'option_type' => $option_type, 'option_name' => $option_name, 'options' => $options];

        $block['polls'][] = &$poll;

        unset($options);

        unset($poll);
    }

    return $block;
}
