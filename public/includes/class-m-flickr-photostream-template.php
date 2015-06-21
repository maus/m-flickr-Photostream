<?php
class MFlickrPhotostreamTemplate {
	private $photostream;
	private $totalItems;
	private $imageSize;
	private $detailed;


	function __construct( $photostream, $totalItems, $imageSize = 'small', $detailed = false ) {
		$this->photostream = $photostream;
		$this->totalItems = $totalItems;
		$this->imageSize = $imageSize;
		$this->detailed = $detailed;
	}


	function display_photos() {
		$output = $this->format_photos();

		echo $output;
	}


	function format_photos() {
		$response = '';
		if ( $this->photostream['stat'] == 'fail' ) {
			$response = "<p id='flickr-error'>" . $this->photostream->err[0]['msg'] . "</p>\n";
		} else {
			$count = 1;
			foreach ( $this->photostream->entry as $entry ) {
				$output = $this->format_photoEntry( $entry );
				$response .= "<li><article>" . $output . "</article></li>\n";
				if ( $count++ == $this->totalItems )
					break;
			}

			$response = "<ul id='" . MFP_SLUG . "'>" . 
					$response . 
				"</ul>";
		}
		
		return $response;
	}


	function format_photoEntry( $entry ) {
		$url = $entry->link[0]['href'];
		$title = htmlspecialchars( $entry->title, ENT_QUOTES );
		$description = $this->format_description( $entry );
		$tags .= $this->format_photoTags( $entry->category );
		$imgURL = $this->format_photoSizeURL( $entry->link[1]['href'] );
		$img = "<img src='" . $imgURL . "' alt='' />";

		if ( $this->detailed ) {
			$output = "<header>
					<h2><a href='" . $url . "' title='" . $title . "'>" . $title . "</a></h2>
				</header>
				<footer>";
					if ( $tags ) {
						$output .= "<span>" . __( 'Tags:', MFP_TEXT_DOMAIN ) . $tags . "</span> ";
					}
					$output .= "<span>" . __( 'Taken:', MFP_TEXT_DOMAIN ) . date( 'd.m.Y', strtotime( $entry->published ) ) . "</span>
				</footer>
				<a href='" . $url . "'>" . $img . "</a>";
				if ( $description ) {
					$output .= "<p>" . $description . "</p>";
				}
		} else {
			$output = "<a href='" . $url . "' title='" . $title . "'>" . $img . "</a>
				<header><a href='" . $url . "' title='" . $title . "'>" . $title . "</a></header>" . 
				$description . 
				"<footer><span>" . __( 'Taken:', MFP_TEXT_DOMAIN ) . date( 'd.m.Y', strtotime( $entry->published ) ) . "</span></footer>";
		}
		
		return $output;
	}

	
	function format_description( $entry ) {
		$content = preg_replace( '/<p>(.*?)<\/p>/', '', $entry->content, 2 );

		return trim( $content );
	}
	

	function format_photoSizeURL( $src ) {
		$size = '';
		switch ( $this->imageSize ) {
			case 'square' :
				$size = '_s';
				break;

			case 'thumbnail' :
				$size = '_t';
				break;

			case 'small' :
				$size = '_m';
				break;

			case 'large' :
				$size = '_b';
				break;

			default :
				$size = '_z';
		}
		
		return str_replace( "_b.", $size . ".", $src );
	}

	
	function format_photoTags( $tags ) {
		$tagsHTML = array();

		if ( count( $tags ) ) {
			foreach ( $tags as $tag ) {
				if ( trim( $tag['term'] ) ) {
					$tagsHTML[] = "<a href='" . $this->format_tagURL( $tag['term'] ) . "'>" . $tag['term'] . "</a>";
				}
			}
		}
		
		return implode( ', ', $tagsHTML );
	}

	
	function format_tagURL( $tag ) { 
		return "http://www.flickr.com/photos/" . $this->userName . "/tags/" . $tag . "/";
	}
}
