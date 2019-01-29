<?php

require_once( "simple_html_dom.php" );


if ( ! class_exists( 'Van_der_Graaf_Page_Generator' ) ) :

class Van_der_Graaf_Page_Generator
{

    /*
     *
     * Perform any initialisation now
     *
     */

    function __construct()
    {
        //  Add our processing filters here

    }


    function build_protected_directory_tree( $root_path, $paths, $htaccess=array() )
    {


        if ( empty( $htaccess ) )
            $htaccess= array(
                "Options -Indexes",
                "DirectoryIndex index.html",
                "<Files *.php>\ndeny from all\n</Files>"
            );

        $final_path = $root_path;

        foreach ( $paths as $path )
        {

            if ( !is_dir( $final_path . '/' . $path ) )
            {
                // echo "<pre>".$final_path . '/' . $path."</pre>";

                @mkdir( $final_path . '/' . $path );
                chmod( $final_path . '/' . $path, 0755 );  // -RWX-R-X-R-X-

            }

            if ( !is_file( $final_path . '/' . $path. '/' . '.htaccess' ) )
            {
                $fo = fopen( $final_path . '/' . $path. '/' . '.htaccess', "w" );

                fputs( $fo, implode( "\n", $htaccess ) );

                fclose( $fo );

                chmod( $final_path . '/' . $path. '/' . '.htaccess', 0444 );  // -R--R---R---
            }

            if ( !is_file( $final_path . '/' . $path. '/' . 'index.html' ) )
            {
                // // echo "<pre> index: ".$final_path . '/' . $path. '/' . 'index.html'."</pre>";

                $fo = fopen( $final_path . '/' . $path. '/' . 'index.html', "w" );

                fputs( $fo, "\n");

                fclose( $fo );

                chmod( $final_path . '/' . $path. '/' . 'index.html', 0644 );  // -R--R---R---
            }

            $final_path .= '/' . $path;


        }

        return $final_path;
    }


    function file_get_html($url, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=false, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
    {
        // We DO force the tags to be terminated.
        $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        // For sourceforge users: uncomment the next line and comment the retreive_url_contents line 2 lines down if it is not already done.
        $contents = $this->get_html( $url );
        // Paperg - use our own mechanism for getting the contents as we want to control the timeout.
        // $contents = retrieve_url_contents($url);

        if (empty($contents) || strlen($contents) > MAX_FILE_SIZE)
        {
            return false;
        }
        // The second parameter can force the selectors to all be lowercase.
        $dom->load($contents, $lowercase, $stripRN);
        return $dom;
    }


    function get_html($url)
    {
        $authorised_header_name = get_option( "VDG_authorised_header_name" );
        $authorised_header_id = get_option( "VDG_authorised_header_id" );

        $ch = curl_init();
        $timeout = 500;
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            strtolower( $authorised_header_name ) .': '.$authorised_header_id
            )
        );

        $data = curl_exec($ch);

        $err = curl_error($ch);

        curl_close($ch);

        return $data;
    }




    function save_file( $filename, $payload = "", $perms = 0644 )
    {
        $fo = fopen( $filename, "w" );

        if ( $fo !== null )
        {
            fputs( $fo, $payload );

            fclose( $fo );

            chmod( $filename, $perms );

            return true;
        }

        return false;

    }

    function split_comment( $src_comment )
    {
//        // echo "<pre>". str_replace( array("\n", "\r", "\r\n", "\n\r" ), array( "", "", "", "" ), $comment) . "</pre>";

        $i = 0;
        $parts = array();
        $in_token = false;
        $token = "";
        $token_start = 0;

        $comment = trim( $src_comment );

        // // echo "<pre>[".$comment."]</pre>";

        while ( $i < strlen( $comment ) )
        {
            $chr = substr( $comment, $i, 1 );

            // // echo "<pre>".$chr."</pre>";

            if ( ord( $chr ) > 32 && ord( $chr ) != 127 )
            {
                if ( $chr == '<' )
                {
                    $in_token = true;
                    $token_start = $i;
                }

                if ( $chr == '>' )
                {
                    if ( $token_start != $i )
                    {
                        $in_token = false;
                        $parts[] = substr( $comment, $token_start, ($i - $token_start + 1  ) );
                    }
                }
            }

            $i++;
        }

        return $parts;
    }


    function from_url( )
    {
        return site_url();
    }


    function from_path()
    {
        $cwd = realpath( dirname( __FILE__ ) );

        write_log( "cwd => ".$cwd ); 


        $parts = explode( DIRECTORY_SEPARATOR, $cwd );

        write_log( $parts );

        // we are 3 folders from the root.  ROOT/WP_CONTENT/PLUGINS/THIS_PLUGIN

        array_pop( $parts );	// ROOT/WP_CONTENT/PLUGINS
        array_pop( $parts );	// ROOT/WP_CONTENT
        array_pop( $parts );	// ROOT

        write_log( $parts );

        $path = implode( DIRECTORY_SEPARATOR, $parts );

        return $path;
    }


    function to_url()
    {
        return get_option( "VDG_STATIC_URL" );
    }


    function to_path( )
    {
        return get_option( "VDG_STATIC_ROOT" );
    }


    function modifyPath( $start = DIRECTORY_SEPARATOR, $mods = "" )
    {
        $final = "";

        $paths1 = explode ( DIRECTORY_SEPARATOR, $start );
        $file1 = array_pop( $paths1 );

        $paths2 = explode ( DIRECTORY_SEPARATOR, $mods );
        $file2 = array_pop( $paths2 );

        $first = array_shift ( $paths2 ) ;

        switch ( $first )
        {
            case "." :
                break;

            case ".." :
                array_pop( $paths1 );

                if ( empty( $paths1 ) )
                    array_unshift( $paths1, "" );

                break;

            default:
                $paths1 = array ( $first ) ;
        }

		// echo "Paths1 = \n".print_r( $paths1, true )."<br >\n";

        while ( !empty( $paths2 ) )
        {
            $seg = array_shift ( $paths2 ) ;

            switch ( $seg )
            {
                case "." :
                    break;

                case ".." :
                    array_pop( $paths1 );

                    if ( empty( $paths1 ) )
                        array_unshift( $paths1, "" );

                    break;

                default:
                    $paths1[] = $seg ;
            }
        }

        if ( $file2 != "" )
            array_push( $paths1, $file2 );
        else
            if ( $file1 != "" )
                array_push( $paths1, $file1 );

        return implode( "/",  $paths1 );
    }

    function replace_urls( $href, $from_url, $to_url )
    {
        return rtrim( str_replace(
                            $from_url,
                            $to_url,
                            $href
                        ), "/");

    }


    function copy_css( $src, $dest, $to_path, $from_path, $from_url, $to_url )
    {

        $file_path = dirname( $src );

		// echo "src = ".$src."\n";
		// echo "dest = ".$dest."\n";

        $file_paths = explode( DIRECTORY_SEPARATOR, $file_path );

        //array_pop( $file_paths );

        $file_path = trailingslashit( implode( "/", $file_paths ) );

        $cssFileContent = file_get_contents( $src );


		$cssFileContent = apply_filters( "vandergraaf_css_filter", $cssFileContent, $file_path, $to_path, $from_path, $from_url, $to_url );


        file_put_contents( $dest, $cssFileContent );

	}



}

endif;
