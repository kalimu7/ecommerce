<?php

namespace Depicter\DataSources\Tags;

use Averta\WordPress\Utility\Plugin;

class ACF extends TagBase implements TagInterface {

	/**
	 *  Asset group ID
	 */
	const ASSET_GROUP_ID = 'acf';

	/**
	 * Get label of asset group
	 *
	 * @return string
	 */
	public function getName(){
		return __( "Advanced Custom Fields", 'depicter' );
	}

	/**
	 * Whether the asset group is enabled (available) or not
	 *
	 * @param array  $args
	 *
	 * @return bool
	 */
	public function isAvailable( array $args = [] ){
		return Plugin::isActive( 'advanced-custom-fields/acf.php' );
	}

	/**
     * Get all acf defined fields
     *
     * @param array $args
	 *
     * @return array $result  List of assets for this module (asset group)
     */
    public function getAssetBlocks( array $args = [] ) {

        $result = [];

        if ( !function_exists( 'acf_get_field_groups' ) ) {
            return $result;
        }

        $queryArgs = [
            'post_type' => 'acf-field',
            'numberposts' => -1,
            'post_status' => 'publish'
        ];

        if ( !empty( $args['postType'] ) ) {
            $fieldGroups = acf_get_field_groups([
                'post_type' => $args['postType']
            ]);

            $postParentIn = [];
            if ( !empty( $fieldGroups ) ) {
                foreach ( $fieldGroups as $fieldGroup ) {
                    $postParentIn[] = $fieldGroup['ID'];
                }
            }

            if ( !empty( $postParentIn ) ) {
                $queryArgs['post_parent__in'] = $postParentIn;
            }
        }

        $fields = get_posts( $queryArgs );

        if ( !empty( $fields ) ) {
            foreach ( $fields as $field ) {
                $content = maybe_unserialize( $field->post_content );
                if ( !empty( $args['inputType'] ) ) {
                    if ( is_array( $args['inputType'] ) ) {
                        if ( !in_array( $content['type'], $args['inputType'] ) ) {
                            continue;
                        }
                    } else {
                        if ( $args['inputType'] != $content['type'] ) {
                            continue;
                        }
                    }
                }

                $result[] = [
                    'id'    => $field->post_excerpt,
                    'title' => $field->post_title,
                    'previewOptions' => [
	                    "size" => 50,
	                    'multiline' => false,
	                    'textSize' => 'regular',
	                    'badge' => 'ACF'
                    ],
                    'type'   => 'dynamicText',
                    'input'  => $content['type'],
                    'func'  => null,
	                'payload' => [
                        'source' => 'acf->' . $field->post_excerpt
                    ]
                ];
            }
        }

        return $result;
    }


	/**
	 * Get value of tag by tag name (slug)
	 *
	 * @param string $tagName  Tag name
	 * @param array  $args     Arguments of current document section
	 *
	 * @return mixed|string
	 */
	public function getSlugValue( string $tagName = '', array $args = [] ){
		if( ! $post = get_post( $args['post'] ?? null ) ){
			return $tagName;
		}

		return get_post_meta( $post->ID, $tagName, true );
	}
}
