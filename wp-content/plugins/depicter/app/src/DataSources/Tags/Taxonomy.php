<?php

namespace Depicter\DataSources\Tags;

/**
 * Asset Group for Wp Taxonomies
 *
 * {{{module->slug|func('a','b')}}}
 *
 */
class Taxonomy extends TagBase {

	/**
	 *  Asset group ID
	 */
	const ASSET_GROUP_ID = 'taxonomy';

	/**
	 * Get label of asset group
	 *
	 * @return string
	 */
	public function getName(){
		return __( "Taxonomies", 'depicter' );
	}

	/**
	 * Get list of assets in this group
	 *
	 * @param array  $args
	 *
	 * @return array
	 */
	public function getAssetBlocks( array $args = [] ){
		if( empty( $args['postType'] ) ){
			return [];
		}
		if( ! $taxonomyObjects = get_object_taxonomies( $args['postType'], 'objects' ) ){
			return [];
		}

		$blocks = [];

		foreach( $taxonomyObjects as $taxonomySlug => $taxonomyObject ){
			if( ! $taxonomyObject->public ?? false ){
				continue;
			}
			$blocks[] = [
				'id'    => $taxonomySlug,
				'title' => $taxonomyObject->label ?? $taxonomyObject->labels->name ?? $taxonomyObject->name ?? 'Undefined',
				'previewOptions' => [
					'size' 	=> 50,
					'variant' => 'simple',
					'badge'	=> null
				],
				'type'  => 'dynamicTagList',
				'func'  => null,
				'payload' => [
					'source' => $args['postType'] . '->' . $taxonomySlug
				]
			];
		}

		return $blocks;
	}

	/**
	 * Get value of tag by tag name (slug)
	 *
	 * @param string $tagName  Tag name
	 * @param array  $args     Arguments of current document section
	 *
	 * @return false|string|\WP_Error|\WP_Term[]
	 */
	public function getSlugValue( string $tagName = '', array $args = [] ){
		if( ! $post = get_post( $args['post'] ?? null ) ){
			return $tagName;
		}

		$tagName = $tagName === 'tag' ? 'post_tag' : $tagName;

		return get_the_terms( $post->ID, $tagName );
	}

	/**
	 * Converts list of terms to string
	 *
	 * @param mixed $pipedValue The tag value
	 * @param array $funcArgs   Function args presented in dynamic tag
	 * @param array $args       Arguments of current document section
	 *
	 * @return mixed|string
	 */
	public function toStr( $pipedValue, array $funcArgs = [], array $args = [] ){
		if ( !empty( $pipedValue ) ) {
			return join( ', ', wp_list_pluck( $pipedValue, 'name') );
		}
		return $pipedValue;
	}
}
