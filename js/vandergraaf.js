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

//console.log( "Javascript included", urls );

var timer = null;

jQuery('document').ready( function ()
{

	var transitionEvent = whichTransitionEvent();

	console.log( "ajax_url = "+ajax_url );
	
	console.log( urls );

	if ( createTable )
	{
		urls.forEach( function ( url )
		{
			// //console.log( url );
			jQuery( '#vandergraaf_processing' )
				.append( "<tr id='"+url.type+"-"+url.id+"'>"+
							"<td width='80%'>"+url.url+"</td>"+
							"<td width='20%' class='"+url.type+"-"+url.id+"'>"+
								"<div class='progress'>"+
									"<div class='"+url.type+"-"+url.id+"-progress progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='"+filter_count+"'></div>"+
								"</div>"+
							"</td>"+
						"<tr>"
						) ;
		});
	}


	if( typeof(EventSource) !== "undefined" )
	{
		var source = new EventSource( ajax_url+'?action=generate_sse' );

		// source.onopen = function() {
		// 	console.log( "Listening" );
		// };
		// source.onerror = function ( e ) {
		// 	//console.log( "Stopped listening" );
		// };

		var count = 0;
		//source.onmessage = function (event) {
		source.addEventListener( 'update' , function ( event )
		{

			// a message without a type was fired
			console.log( "update", event.data );

			data = JSON.parse( event.data );

			jQuery( "."+data.type+"-"+data.id+"-progress" ).attr( "aria-valuenow", ""+( (Math.min( data.status, filter_count ) / filter_count) * 100)+"%"  );

			jQuery( "."+data.type+"-"+data.id+"-progress" ).css( "width", ""+( (Math.min( data.status, filter_count ) / filter_count) * 100)+"%" );

			if ( data.status >= filter_count )
			{
				jQuery("."+data.type+"-"+data.id+"-progress").one( transitionEvent,
					function(event) {
						jQuery( event.target ).removeClass( "active").removeClass( "progress-bar-striped").addClass( "progress-bar-success" );
					}
				);

			}


		} );

		source.addEventListener( 'starting' , function ( event )
		{

			// a message without a type was fired
			console.log( "starting", event.data );

		} );

		source.addEventListener( 'completed' , function ( event )
		{

			// a message without a type was fired
			console.log( "completed", event.data );

			source.close();

		} );

	}
	else
	{

		console.log( "Non SSE" );
		updateStatuses( );  // start our progress check

		var data = {
			'action': 'generate'
		};

		jQuery.get(ajax_url+'?action=generate')
			.done( function(response)
		{
			data = JSON.parse( response );

			clearTimeout( timer );
		});

	}

});

function updateStatuses( )
{
	var data = {
		'action': 'getstatus'
	};

	var carry_on = true;

	var self = this;

	//console.log( "ajax url", ajax_url+'?action=getstatus' )
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.get(ajax_url+'?action=getstatus')
		.done( function(response)
	{

		// console.log( "filter_count", filter_count );

		var completed = 0;

		if ( response !== null )
		{
			data = JSON.parse( response );

			if ( data.length > 0 )
			{
				data.forEach( function ( url )
				{
					if ( url.status >= filter_count )
						completed++;

					jQuery( "."+url.type+"-"+url.id+"-progress" ).attr( "aria-valuenow", ""+( (Math.min( url.status, filter_count ) / filter_count) * 100)+"%"  );

					jQuery( "."+url.type+"-"+url.id+"-progress" ).css( "width", ""+( (Math.min( url.status, filter_count ) / filter_count) * 100)+"%" );

					if ( url.status >= filter_count )
					{
						jQuery("."+url.type+"-"+url.id+"-progress").one( transitionEvent,
							function(event)
							{
								jQuery( event.target ).removeClass( "active").removeClass( "progress-bar-striped").addClass( "progress-bar-success" );
							}
						);
					}


				} );

				if ( completed >= url.length )
					self.carry_on = false;
			}


	}

		// console.log( "completed", completed );

		if ( completed < data.length )
			timer = setTimeout( updateStatuses, 200 );

	});

}

// Function from David Walsh: http://davidwalsh.name/css-animation-callback
function whichTransitionEvent(){
  var t,
      el = document.createElement("fakeelement");

  var transitions = {
    "transition"      : "transitionend",
    "OTransition"     : "oTransitionEnd",
    "MozTransition"   : "transitionend",
    "WebkitTransition": "webkitTransitionEnd"
  }

  for (t in transitions){
    if (el.style[t] !== undefined){
      return transitions[t];
    }
  }
}


function whichAnimationEvent(){
  var t,
      el = document.createElement("fakeelement");

  var animations = {
    "animation"      : "animationend",
    "Oanimation"     : "oanimationEnd",
    "Mozanimation"   : "animationend",
    "WebkitAnimation": "webkitAnimationEnd"
  }

  for (t in animations){
    if (el.style[t] !== undefined){
      return animations[t];
    }
  }
}

