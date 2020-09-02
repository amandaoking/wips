<?php
/**
 * Plugin Name: Dashcam
 * Plugin URI: 
 * Description: Add RSS feeds to dashboard.
 * Author: PC Gamer Girl (A.King)
 * Author URI: 
 * Version: 1.0.0
 *
 * Copyright 2017 ( email : info@pcgamergirl.com )
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

add_action('wp_dashboard_setup', 'dc_plugin_add_dashboard_widgets' );

function dc_plugin_add_dashboard_widgets()
{
	wp_add_dashboard_widget('dc_plugin_dashboard_widget', 'Dashcam', 'dc_plugin_dashboard_widget');	
}

function dc_plugin_dashboard_widget()
{
	$feed_urls = array();
	$num = 5;
	
	$dc_plugin_settings = get_option('dc_plugin_settings');
	
	if( $dc_plugin_settings !== FALSE && $dc_plugin_settings != "" && isset($dc_plugin_settings['urls']) && count($dc_plugin_settings['urls']) > 0 )
	{
		$feed_urls = ( (isset($dc_plugin_settings['urls'])) ? $dc_plugin_settings['urls'] : array() );
		$num = ( (isset($dc_plugin_settings['num']) && $dc_plugin_settings['num'] != "") ? $dc_plugin_settings['num'] : 5 );
	}
?>
<div id="dc-configure-button" class="wp-editor-wrap hide-if-no-js" style="margin-bottom:15px">
	<a href="#" class="button dc-configure" title="Configure Watched Blogs" style="padding-right:15px;"><div style="float:left; margin:0; padding:0; margin-top:-4px; margin-left:-8px; width:32px; height:32px; background:transparent url(<?php echo admin_url(); ?>/images/menu.png) no-repeat -209px -33px;"></div>Configure</a>
</div>
<div id="dc-configure" style="display:none">
	
	<h4>Configure</h4>
	<br />
	<div id="dc-configure-errors" style="display:none; margin-bottom:15px; background-color:#FFF3F3; border:1px solid #900; padding:10px; font-weight:bold"></div>
	Enter RSS Feed URLs:
	<div class="input-text-wrap" id="dc-feed-urls" style="margin-top:7px; margin-bottom:5px;">
	<?php
		if( count($feed_urls) )
		{
			foreach($feed_urls as $feed_url)
			{
				echo '<input type="text" name="feed_urls[]" value="'.$feed_url.'" style="width:90%"> <a href="#" class="dc-remove-url">-</a>';
			}
		}
		else
		{
			// echo blank
			echo '<input type="text" name="feed_urls[]" value="" style="width:90%"> <a href="#" class="dc-remove-url">-</a>'; 
		}
	?>
	</div>
	<a href="#" class="button" id="dc-add-url">+ Add URL</a>
	<br /><br />
	Show <input type="number" name="num" id="dc-num" value="<?php echo $num; ?>" min="1" max="100" style="width:50px"> entries
	<br /><br />
	<em style="color:#999">Note: You can enter a direct link to the site's RSS/ATOM feed, or enter the normal website address. If you enter the normal website address we'll attempt to locate the RSS/ATOM feed on your behalf.</em>
	<br /><br />
	<p class="submit" align="right">
		<span id="save_loading" style="display:none"><img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" alt="Loading..."></span>&nbsp;
		<a href="#" class="cancel">Cancel</a>&nbsp;
		<a href="#" class="button-primary">Update</a>
	</p>
	<div style="clear:both"></div>
</div>
<div id="dc-live-feed">Loading...</div>

<script type="text/javascript">

	var saving = false;
	jQuery(document).ready(function($) 
	{
		// Show config panel
		$('#dc_plugin_dashboard_widget').on('click', '.dc-configure', function()
		{
			$('#dc-configure-button').fadeOut('fast');
			$('#dc-live-feed').fadeOut('fast', function()
			{
				$('#dc-configure').fadeIn('fast');
				$('#dc-feed-urls input[value=\'\']:first').focus();
			});
				
			return false;
		});
		
		$('#dc_plugin_dashboard_widget').on('click', '#dc-add-url', function()
		{
			$('#dc-feed-urls').append('<input type="text" name="feed_urls[]" value="" style="width:90%; display:none">');
			$('#dc-feed-urls input:last-child').fadeIn('fast', function()
			{
				$('#dc-feed-urls').append(' <a href="#" class="dc-remove-url">-</a>');
				$('#dc-feed-urls input:last-child').focus();
			});
		});
		
		$('#dc_plugin_dashboard_widget').on('click', '.dc-remove-url', function()
		{
			var input_to_remove = $(this).prev();
			input_to_remove.fadeOut('fast', function()
			{
				input_to_remove.remove();
			});
			$(this).fadeOut('fast', function()
			{
				$(this).remove();
			});
		});
		
		$('#dc_plugin_dashboard_widget').on('keydown', 'input[name=\'feed_urls[]\']', function()
		{
			$('input[name=\'feed_urls[]\']').css("backgroundColor", '#FFFFFF');
		});
		
		// save config panel
		$('#dc_plugin_dashboard_widget .submit a.button-primary').click(function()
		{
			if( !saving )
			{
				saving = true;
				$('#save_loading').show();
				$('#dc-configure-errors').slideUp('fast');
				
				var feed_urls_array = new Array();
				
				$('input[name=\'feed_urls[]\']').each(function(i)
				{
					if( $(this).val() != "" )
					{
						var feed_url_to_add = $(this).val()
						if (feed_url_to_add.toLowerCase().indexOf("http://") == -1 && feed_url_to_add.toLowerCase().indexOf("https://") == -1)
						{
							feed_url_to_add = "http://" + feed_url_to_add
						}
						feed_urls_array.push(feed_url_to_add);
					}
				});
				
				var data = {
					action: 'dc_save',
					feed_urls: feed_urls_array,
					num: $('#dc-num').val()
				};
			
				$.post(ajaxurl, data, function(response) 
				{
					saving = false;
					$('#save_loading').hide();
					
					if (response != "")
					{
						if (response != "OK")
						{
							response = response.split("|");
							if (response[0] == "invalid")
							{
								var invalid_urls = response[1].split("^^^");
								
								$('#dc-configure-errors').html( invalid_urls.length + " of the URL's entered weren't valid RSS/ATOM feeds, or didn't contain a &lt;meta&gt; link pointing to their RSS/ATOM feed.");
								$('#dc-configure-errors').slideDown('fast');
								
								// urls entered are invalid
								$('input[name=\'feed_urls[]\']').each(function(i)
								{
									for ( var i in invalid_urls )
									{
										if ( $(this).val() == invalid_urls[i] )
										{
											// if invalid, highlight url
											$(this).css("backgroundColor", "#FFF3F3")
										}
									}
								});
							}
						}
						else
						{
							var data = {
								action: 'dc_get_feed'
							};
							
							$('#dc-live-feed').html("Loading...");
						
							$.post(ajaxurl, data, function(response) 
							{
								if (response == "")
								{
									$('#dc-live-feed').html("No blog entries found in any of the configured feeds.");
								}
								else
								{
									$('#dc-live-feed').html(response);
								}
							});
		
							$('#dc-configure').fadeOut('fast', function()
							{
								$('#dc-configure-button').fadeIn('fast');
								$('#dc-live-feed').fadeIn('fast');
							});
						}
					}
					else
					{

					}
				});
			}
			
			return false;
		});
		
		$('#dc_plugin_dashboard_widget .submit a.cancel').click(function()
		{
			$('#dc-configure').fadeOut('fast', function()
			{
				$('#dc-configure-button').fadeIn('fast');
				$('#dc-live-feed').fadeIn('fast');
			});
		});
		
	<?php
		$dc_plugin_settings = get_option('dc_plugin_settings');
	
		if( count($feed_urls) )
		{
	?>
		var data = {
			action: 'dc_get_feed'
		};
		
		$('#dc-live-feed').html("Loading...");
		
		$.post(ajaxurl, data, function(response) 
		{
			if (response == "")
			{
				$('#dc-live-feed').html("No blog entries found in any of the configured feeds.");
			}
			else
			{
				$('#dc-live-feed').html(response);
			}
		});
	<?php
		}
		else
		{
	?>
		$('#dc-live-feed').html("Please click '<a href=\"#\" class=\"dc-configure\">Configure</a>' and enter the feed URLs that you wish to watch.");
	<?php
		}
	?>
	});
</script>
<?php
}

add_action('wp_ajax_dc_save', 'dc_save_callback');

function dc_save_callback() 
{
	$invalid_urls = array();
	$valid_urls = array();
	
	// loop through
	if( isset($_POST['feed_urls']) && count($_POST['feed_urls']) )
	{
		$existing_feed_urls = array();
		
		$dc_plugin_settings = get_option('dc_plugin_settings');
		
		if( $dc_plugin_settings !== FALSE && $dc_plugin_settings != "" && isset($dc_plugin_settings['urls']) && count($dc_plugin_settings['urls']) > 0 )
		{
			$existing_feed_urls = ( (isset($dc_plugin_settings['urls'])) ? $dc_plugin_settings['urls'] : array() );
		}
		
		foreach( $_POST['feed_urls'] as $feed_url )
		{
			$valid = false;
			
			if( in_array($feed_url, $existing_feed_urls) )
			{
				$valid = true;
			}
			else
			{
				$feed = @file_get_contents($feed_url);
				
				if( $feed !== FALSE && $feed != "" )
				{
					
					$xml = @simplexml_load_string($feed);
					
					if( $xml !== FALSE )
					{
						// The page returned XML. Now check for the two 
						if( count($xml) > 0 )
						{
							// If RSS
							if( isset($xml->channel))
							{
								$valid = true;
							}
							// If Atom
							if( isset($xml->entry))
							{
								$valid = true;
							}
						}
					}
					
					if( $valid === FALSE )
					{
						$rss_link = dc_get_rss_location($feed, $feed_url);
						
						if( $rss_link !== FALSE )
						{
							$valid = true;
							$feed_url = $rss_link;
						}
					}
				}
			}
			
			if( $valid === FALSE )
			{
				$invalid_urls[] = $feed_url;
			}
			else
			{
				$valid_urls[] = $feed_url;
			}
		}
	}

	if ( count($invalid_urls) == 0 )
	{
		$options = array();
		$options['urls'] = $valid_urls;
		
		$_POST['num'] = preg_replace('(\D+)', '', $_POST['num']);
		
		$options['num'] = ( (isset($_POST['num']) && $_POST['num'] != "" && $_POST['num'] >= 1 && $_POST['num'] <= 100) ? $_POST['num'] : 5 );
		
		update_option( 'dc_plugin_settings', $options );
		
		echo 'OK';
	}
	else
	{
		echo 'invalid|'.implode("^^^", $invalid_urls);
	}
	
	die();
}

function dc_get_rss_location($html, $location)
{
    if( !$html || !$location )
    {
        return false;
    }
    else
    {
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for($n=0; $n<$link_count; $n++){
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach($attributes as $attribute){
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if(isset($att[1])){
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
		
        for($n=0; $n<$link_count; $n++){
            if(strtolower($final_links[$n]['rel']) == 'alternate'){
                if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
                    $href = $final_links[$n]['href'];
                }
                if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
                    #kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if($href){
                    if(strstr($href, "http://") !== false){ 
                        $full_url = $href;
                    }else{ #otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        $full_url = "http://$url_parts[host]";
                        if(isset($url_parts['port'])){
                            $full_url .= ":$url_parts[port]";
                        }
                        if($href{0} != '/'){ 
                            $full_url .= dirname($url_parts['path']);
                            if(substr($full_url, -1) != '/'){
                                #if the last character isn't a '/', add it
                                $full_url .= '/';
                            }
                        }
                        $full_url .= $href;
                    }
                    return $full_url;
                }
            }
        }
        return false;
    }
}

add_action('wp_ajax_dc_get_feed', 'dc_get_feed_callback');

function dc_get_feed_callback() 
{
   	$dc_plugin_settings = get_option('dc_plugin_settings');
	
	$num = 5;
	
	if( $dc_plugin_settings !== FALSE && $dc_plugin_settings != "" )
	{
		$errors = array();
		$items = array();
		
		$feed_urls = ( (isset($dc_plugin_settings['urls'])) ? $dc_plugin_settings['urls'] : array() );
		$num = ( (isset($dc_plugin_settings['num'])) ? $dc_plugin_settings['num'] : 5 );
		
		if( count( $feed_urls ) > 0 )
		{
			foreach( $feed_urls as $feed_url )
			{
				$feed = @file_get_contents($feed_url);
				
				if( $feed !== FALSE && $feed != "" )
				{
					$xml = @simplexml_load_string($feed);
					
					if( $xml !== FALSE )
					{
						if( count($xml) > 0 )
						{
							// If RSS
							if( isset($xml->channel->item) && count($xml->channel->item) > 0 )
							{
								$i = 0;
								foreach( $xml->channel->item as $item )
	       						{
	       							if ($i >= $num)
									{
										break;
									}
									
									$items[strtotime($item->pubDate)] = '<li>
										<a class="rsswidget" href="' . $item->link . '" title="' . $item->description . '" target="_blank">' . $item->title . '</a>
										<br /><span class="rss-date" style="margin:0; padding:0">' . date("F d, Y", strtotime($item->pubDate)) . '</span>
										<div class="rssSummary">' . $item->description . '<br /><span style="color:#888">Source: ' . $feed_url . '</span></div>
									</li>';
									
									++$i;
								}
							}
							// If Atom
							if( isset($xml->entry) && count($xml->entry) > 0 )
							{
								$i = 0;
								
								foreach( $xml->entry as $item )
	       						{
	       							if ($i >= $num)
									{
										break;
									}
									
									$itemLink = '';
									foreach ($item->link as $link) {
					                    $itemLink = $link['href'] . '';
					                    break;
					                }
									
									$items[strtotime($item->updated)] = '<li>
										<a class="rsswidget" href="' . $itemLink . '" title="' . $item->summary . '" target="_blank">' . $item->title . '</a>
										<br />
										<span class="rss-date">' . date("F d, Y", strtotime($item->updated)) . '</span>
										<div class="rssSummary">' . $item->summary . '<br /><span style="color:#888">Source: ' . $feed_url . '</span></div>
									</li>';
									
									++$i;
								}
							}
						}
					}
				}
				else
				{
					$errors[] = '<div class="error">The feed at '.$feed_url.' doesn\'t exist, has moved or is empty.</div>';
				}
			}
		}
	}
	
	if( count($errors) > 0 )
	{
		echo $errors;
	}
	
	if( count($items) > 0 )
	{
		// sort by date, get num
		ksort($items); 
		$items = array_reverse($items);
		$items = array_slice($items, 0, $num);
		
		echo '<div class="rss-widget"><ul>' . implode("", $items) . '</ul></div>';
	}
	die();
}
 
// Activation / Deactivation / Deletion
register_activation_hook( __FILE__, 'dc_plugin_activation' );
register_deactivation_hook( __FILE__, 'dc_plugin_deactivation' );

function dc_plugin_activation()
{
	// Add option for storing plugin setting
	update_option( 'dc_plugin_settings', array() );
	
	//register uninstaller
    register_uninstall_hook( __FILE__, 'dc_plugin_uninstall' );
}

function dc_plugin_deactivation()
{    
	// Actions to perform once on plugin deactivation go here
}

function dc_plugin_uninstall()
{
	 delete_option('dc_plugin_settings');
}