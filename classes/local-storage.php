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

//require_once( plugin_dir_path( __FILE__ ) . "storage.php" );

class Van_der_Graaf_Local_Storage // extends Van_der_Graaf_Storage
{


    function __construct()
    {

    }

    function writeFile( $path, $payload, $perms )
    {
        
        $fo = fopen( $path, "w" );

        if ( $fo !== null )
        {
            fputs( $fo, $payload );

            fclose( $fo );

            chmod( $path, $perms );

            return true;
        }

        return false;
    }


    function createFolder( $root_path, $paths )
    {
        
        $htaccess = array (
            "Options -Indexes",
            "DirectoryIndex index.html",
            "<Files *.php>\ndeny from all\n</Files>"
        );


        $final_path = $root_path;

        foreach ( $paths as $path )
        {

            if ( !is_dir( $final_path . '/' . $path ) )
            {
                //echo "<pre>".$final_path . '/' . $path."</pre>";

                // write_log( $final_path );
                mkdir( $final_path . '/' . $path, 0755, true );
                // chmod( $final_path . '/' . $path, 0755 );  // -RWX-R-X-R-X-

            }

            if ( !is_file( $final_path . '/' . $path. '/' . '.htaccess' ) )
            {
                // $fo = fopen( $final_path . '/' . $path. '/' . '.htaccess', "w" );

                // fputs( $fo, implode( "\n", $htaccess  ) );

                // fclose( $fo );

                // chmod( $final_path . '/' . $path. '/' . '.htaccess', 0444 );  // -R--R---R---

                $this->writeFile( $final_path . '/' . $path. '/' . '.htaccess',
                                                    implode( "\n", $htaccess  ) , 
                                                    0444  );
            }

            if ( !is_file( $final_path . '/' . $path. '/' . 'index.html' ) )
            {
                // // echo "<pre> index: ".$final_path . '/' . $path. '/' . 'index.html'."</pre>";

                // $fo = fopen( $final_path . '/' . $path. '/' . 'index.html', "w" );

                // fputs( $fo, "\n");

                // fclose( $fo );

                // chmod( $final_path . '/' . $path. '/' . 'index.html', 0644 );  // -R--R---R---

                $this->writeFile( $final_path . '/' . $path. '/' . 'index.html', PHP_EOL, 0644 );

            }

            $final_path .= '/' . $path;


        }

        return $final_path;

    }

    function copyFile( $src, $dest )
    {
        copy( $src, $dest );
    }
}