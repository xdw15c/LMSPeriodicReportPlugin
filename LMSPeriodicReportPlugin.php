<?php

/*
 *  LMS version 1.11-git
 *
 *  Copyright (C) 2001-2017 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

/**
 * LMSPeriodicRaportPlugin
 *
 * @author Tomasz Chiliński <tomasz.chilinski@chilan.com>
 * made as plugin by Grzegorz Cichowski <gcichowski@gmail.com>
 */
class LMSPeriodicReportPlugin extends LMSPlugin {
	const plugin_directory_name = 'LMSPeriodicReportPlugin';
	const PLUGIN_NAME = 'Periodic Report';
	const PLUGIN_DESCRIPTION = 'Financial data export';
	const PLUGIN_AUTHOR = 'Tomasz Chiliński &lt;tomasz.chilinski@chilan.com&gt;';

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'PeriodicReportInitHandler',
				'method' => 'smartyInit',
			),
			'modules_dir_initialized' => array(
				'class' => 'PeriodicReportInitHandler',
				'method' => 'ModulesDirInit',
			),
			'menu_initialized' => array(
				'class' => 'PeriodicReportInitHandler',
				'method' => 'menuInit',
			),
			'access_table_initialized' => array(
				'class' => 'PeriodicReportInitHandler',
				'method' => 'accessTableInit',
			),
			'1' => array(
				'class' => 'LMSCustomer',
				'method' => '__construct',
			),
			'2' => array(
				'class' => 'LMSCustomer',
				'method' => 'getID',
			),
			'customer_collection' => array(
				'class' => 'LMSCustomerCollection',
				'method' => 'addCustomer',
			),
		);
	}
}

?>
