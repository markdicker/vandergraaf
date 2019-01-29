<?php

/**
*	Copyright (C) 2012-2017 Mark Dicker (email: mark@markdicker.co.uk)
*
*	This program is free software; you can redistribute it and/or
*	modify it under the terms of the GNU General Public License
*	as published by the Free Software Foundation; either version 2
*	of the License, or (at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program; if not, write to the Free Software
*	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once( plugin_dir_path( __FILE__ ) . "wp-async-request.php" );
require_once( plugin_dir_path( __FILE__ ) . "wp-background-process.php" );

class Van_der_Graaf_Process_Request extends WP_Async_Request
{
    protected $action = 'page_builder_request';

    protected function handle() {
        $type = $_POST['type'];
        $id = $_POST['id'];

        do_action( "vandergraaf_generate_{$type}", $id );

    }
}

class Van_der_Graaf_Process_Page extends WP_Background_Process 
{
    
    /**
     * @var string
     */
    protected $action = 'page_builder';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        // Actions to perform

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }

}