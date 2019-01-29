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

if ( ! class_exists( 'Van_der_Graaf_Settings' ) ) :

class Van_der_Graaf_Settings 
{

    function __construct( )
    {

    }

    function textField( $args )
    {
        $value = get_option( $args['opt_name'], $args['placeholder'] );

        if ( $value == "" )
            $value = $args['placeholder'];

		// Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field
		$html = '<input type="text" id="'. $args['opt_name'].'" name="'. $args['opt_name'] .'" value="' . $value . '" placeholder="'. $args['placeholder'] .'" />';
		 
		// Here, we will take the first argument of the array and add it to a label next to the checkbox
		// $html .= '<label for="'. $args['opt_name'] . '"> '  . $args['label'] . '</label>';
		 
		echo $html;		 

    }

    function passwordField( $args )
    {
		// Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field
		$html = '<input type="password" id="'. $args['opt_name'].'" name="'. $args['opt_name'] .'" value="' . get_option( $args['opt_name'] ) . '" placeholder="'. $args['placeholder'] .'" />';
		 
		// Here, we will take the first argument of the array and add it to a label next to the checkbox
		// $html .= '<label for="'. $args['opt_name'] . '"> '  . $args['label'] . '</label>';
		 
		echo $html;		 

    }

    function checkboxField( $args )
    {
		// Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field
		$html = '<input type="checkbox" id="'. $args['opt_name'].'" name="'. $args['opt_name'] .'" value="Y" '.checked( get_option( $args['opt_name'] ), 'Y', false ) .' />';
		 
		// Here, we will take the first argument of the array and add it to a label next to the checkbox
		// $html .= '<label for="'. $args['opt_name'] . '"> '  . $args['label'] . '</label>';
		 
		echo $html;		 

    }

    function selectField( $args )
    {
		// Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field
		$html = '<select id="'. $args['opt_name'].'" name="'. $args['opt_name'] .'"  />';
        
        $cur_selection = get_option( $args['opt_name'] );
        
        foreach ( $args['opt_val'] as $option => $value ) 
        {

            //$html .= "<!-- ".print_r( $value, true )." -->";

            if ( is_array( $value ) )
            {
                $opt_name = $value[0];
            }
            else
            {
                $opt_name = $value;
            }

            $html .= '<option value="'.$option.'" '.selected( $cur_selection, $option, false ) .'>'.$opt_name.'</option>';
        }

        $html .= '</select>';

		// Here, we will take the first argument of the array and add it to a label next to the checkbox
		// $html .= '<label for="'. $args['opt_name'] . '"> '  . $args['label'] . '</label>';
		 
		echo $html;		 

    }

    function addSection( $id, $title, $callback, $page, $saved = null )
    {
        add_settings_section( $id, $title, $callback, $page );


        write_log( $saved );

        if ( $saved !== null )
            add_action('update_options_'.$id,$saved,10, 2);
    }


    function addField( $section, $page, $args )
    {

        $fieldHandler = null;

        switch( $args['type'] )
        {
            case 'text' :
                $fieldHandler = array( $this, "textField" );
                break;
            case 'checkbox' :
                $fieldHandler = array( $this, "checkboxField" );
                break;
            case 'select' :
                $fieldHandler = array( $this, "selectField" );
                break;
            case 'password' :
                $fieldHandler = array( $this, "passwordField" );
                break;
        }

        add_settings_field ( 
			$args['opt_name'],
			$args['label'],
			$fieldHandler,
			$page,
            $section,
            $args
		);

        register_setting( $page, $args['opt_name'] );

    }

    function fieldSanitation( $input )
    {
        echo "<pre>".print_r( $input, true )."</pre>";

        return $input;
    }
}

endif;
