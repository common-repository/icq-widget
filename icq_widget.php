<?php
/*
Plugin Name: ICQ Widget
Plugin URI: http://zakatnov.ru/icq_widget
Description: Добавляет на сайдбар виджет для отправки администратору сайта сообщения ICQ
Version: 1.0.0
Author: Alexander Zakatnov
Author URI: http://zakatnov.ru
License: GPL

This software comes without any warranty, express or otherwise, and if it
breaks your blog or results in your cat being shaved, it's not my fault.

Copyright 2009  Alexander A. Zakatnov  (email: alexander@zakatnov.ru)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


*/

//require_once("icq_class/rtf.class.php");
require_once("icq_class/WebIcqPro.class.php");
function icq_widget($args) {
	extract($args);
	$options = get_option('icq_widget');
	$uin = $options['uin'];
	$pass = $options['pass'];
	$uin_rec = $options['uin_rec'];
	$cols = $options['cols'];
	$rows = $options['rows'];
	$widget_title = $options['widget_title'];

	if(isset($_POST['icq_text']))
	{		$text = strip_tags(stripslashes($_POST['icq_text']));
		if(function_exists(iconv))
		{			$text = iconv("UTF-8", "WINDOWS-1251", $text);
		}
		$icq = new WebIcqPro();
		$icq->connect($uin, $pass);
		if($icq->isConnected())
		{
			if($icq->sendMessage($uin_rec, $text))
			{				$result = "Message was successfully sent!";
			}
			else
			{				$result = "Error sending. Please try again later.";
			}
			$icq->disconnect();
		}
		else
		{			$result = $icq->error;
		}
	}
	echo $before_widget ;
	echo "<div id='icq_div'>"
             .$before_title.$widget_title.$after_title;
	echo "<form method='POST'>
	<textarea name='icq_text' rows='".$rows."' cols='".$cols."'></textarea><br>
	<input type='submit' value='отправить'><p style='font-size: 0.9em; font-color: #333;'>".$result."</p>
	</form></div>";
	echo $after_widget;
}

function icq_widget_options() {
	$options = get_option('icq_widget');
	if ( !is_array($options) )
		$options = array('uin'=>'111111', 'pass'=>'111111', 'uin_rec'=>'111111', 'rows'=>'10', 'cols'=>'25');
	if (isset($_POST['uin'])) {
		$options['uin'] = strip_tags(stripslashes($_POST['uin']));
		$options['pass'] = strip_tags(stripslashes($_POST['pass']));
		$options['uin_rec'] = strip_tags(stripslashes($_POST['uin_rec']));
		$options['cols'] = strip_tags(stripslashes($_POST['cols']));
		$options['rows'] = strip_tags(stripslashes($_POST['rows']));
		$options['widget_title'] = strip_tags(stripslashes($_POST['widget_title']));
		update_option('icq_widget', $options);
	}
	$uin = htmlspecialchars($options['uin'], ENT_QUOTES);
	$pass = htmlspecialchars($options['pass'], ENT_QUOTES);
	$uin_rec = htmlspecialchars($options['uin_rec'], ENT_QUOTES);
	$rows = htmlspecialchars($options['rows'], ENT_QUOTES);
	$cols = htmlspecialchars($options['cols'], ENT_QUOTES);
	$widget_title = htmlspecialchars($options['widget_title'], ENT_QUOTES);
	echo '<p style="text-align:right;">
			<label for="widget_title">' . __('Widget header') . '
			<input style="width: 200px;" id="widget_title" name="widget_title" type="text" value="'.$widget_title.'" />
			</label></p>';
	echo '<p style="text-align:right;">
			<label for="uin">' . __('Number from which the message is sent') . '
			<input style="width: 200px;" id="uin" name="uin" type="text" value="'.$uin.'" />
			</label></p>';
	echo '<p style="text-align:right;">
			<label for="pass">' . __('Password sender number') . '
			<input style="width: 200px;" id="pass" name="pass" type="password" value="'.$pass.'" />
			</label></p>';
	echo '<p style="text-align:right;">
			<label for="uin_rec">' . __('Number ICQ message receiver') . '
			<input style="width: 200px;" id="uin_rec" name="uin_rec" type="text" value="'.$uin_rec.'" />
			</label></p>';
	echo '<p style="text-align:right;">
			<label for="rows">' . __('Number of rows in the form') . '
			<input style="width: 200px;" id="rows" name="rows" type="text" value="'.$rows.'" />
			</label></p>';
	echo '<p style="text-align:right;">
			<label for="cols">' . __('Number of cols in the form of') . '
			<input style="width: 200px;" id="cols" name="cols" type="text" value="'.$cols.'" />
			</label></p>';
}

function register_icq_widget()
{	register_sidebar_widget(array('ICQ', 'widgets'), 'icq_widget');
	register_widget_control(array('ICQ', 'widgets'), 'icq_widget_options', 500, 600);
}

add_action('init', 'register_icq_widget');
?>