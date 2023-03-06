<?php
namespace Depicter\DataSources;

use Averta\Core\Utility\Data;
use Averta\WordPress\Utility\JSON;
use Averta\Core\Utility\Str;
class Products extends Posts implements DataSourceInterface {

	/**
	 * DataSource name
	 *
	 * @var string
	 */
	protected $type = 'wooProducts';

	/**
	 * DataSource properties
	 *
	 * @var array
	 */
	protected $properties = [
		'type'     => 'wooProducts',
		'postType' => 'product'
	];

	/**
	 * Default input params for retrieving dataSource records
	 *
	 * @var array
	 */
	protected $defaultInputParams = [
		'postType' => 'product',
		'perpage' => 5,
		'excerptLength' => 100,
		'offset' => 0,
		'linkSlides' => true,
		'orderBy' => 'date',
		'order' => 'DESC',
		'imageSource' => 'featured',
		'excludedIds' => '',
		'includedIds' => '',
		'excludeNonThumbnail' => true,
		'taxonomies' => '',
        'from' => 'all', // product types - available options => all, best-selling, top-rated, featured, on-sale
        'inStockOnly' => true,
        'regularProducts' => true,
        'downloadableProducts' => true,
        'virtualProducts' => true,
		'filterByPrice' => false,
        'startPrice' => '',
        'endPrice' => '',
        'startSalePrice' => '',
        'endSalePrice' => ''
	];

	/**
	 * Asset groups of this DataSource
	 *
	 * @var array
	 */
	protected $assetGroupNames = [ 'post', 'product', 'taxonomy', 'acf', 'metaboxio' ];

    /**
	 * Retrieves the list of records based on query params
	 *
	 * @param $args
	 *
	 * @return \WP_Query
	 */
	protected function getRecords( $args ){

		$queryArgs = [
		    'post_type'       => $args['postType'],
		    'posts_per_page'  => $args['perpage'],
		    'order'           => $args['order'],
		    'orderby'         => $args['orderBy'],
		    'offset'          => $args['offset'],
		    'post__in'        => $args['includedIds'],
		    'post__not_in'    => $args['excludedIds'],
		    'tax_query'       => [],
			'meta_query'      => []
	    ];

		if ( !empty( $args['taxonomies'] ) ) {
			$taxonomies = $args['taxonomies'];

			if( JSON::isJson( $args['taxonomies'] ) ){
				$taxonomies = JSON::decode( $args['taxonomies'] );
			}

			if( !empty( $taxonomies->product_cat ) ){
				$queryArgs['tax_query'][] = [
					'taxonomy'  => 'product_cat',
					'field'     => 'slug',
					'terms'     => $taxonomies->product_cat
				];
			}

			if( !empty( $taxonomies->product_tag ) ){
				$queryArgs['tax_query'][] = [
					'taxonomy'  => 'product_tag',
					'field'     => 'slug',
					'terms'     => $taxonomies->product_tag
				];
			}
	    }

        if ( !empty( $args['from'] ) ) {
            switch ( $args['from'] ) {
                case 'featured':
                    $queryArgs['tax_query'][] = array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    );
                    break;

                case 'best-selling':
                    $queryArgs['orderby'] = 'meta_value_num';
                    break;

                case 'on-sale':
                    $queryArgs['meta_query'] = WC()->query->get_meta_query();
                    $queryArgs['post__in'] = wc_get_product_ids_on_sale();
                    break;

                case 'top-rated':
                    $queryArgs['meta_query'] = WC()->query->get_meta_query();
                    add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
                    break;
                default: // i.e. all products
                    $queryArgs['meta_query'] = WC()->query->get_meta_query();
                    break;
            }
        }

		if( Data::isTrue( $args['excludeNonThumbnail'] ) ){
			$queryArgs['meta_query'][] = [
	    		'key'     => '_thumbnail_id',
                'compare' => 'EXISTS'
		    ];
		}

        if ( Data::isTrue( $args['inStockOnly'] ) ) {
            $queryArgs['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '=',
            ];
        }

		if ( ! Data::isTrue( $args['regularProducts'] ) ) {
			if ( ! Data::isTrue( $args['downloadableProducts'] ) && ! Data::isTrue( $args['virtualProducts'] ) ) {
				return new \WP_Query();
			}

			if ( Data::isTrue( $args['downloadableProducts'] ) ) {

				if ( Data::isTrue( $args['virtualProducts'] ) ) {
					$queryArgs['meta_query'][] = [
						'relation' => 'OR',
						[
							'key' => '_downloadable',
							'value' => 'yes',
							'compare' => '==',
						],
						[
							'key' => '_virtual',
							'value' => 'yes',
							'compare' => '==',
						]

					];
				} else {
					$queryArgs['meta_query'][] = [
						'key' => '_downloadable',
						'value' => 'yes',
						'compare' => '==',
					];
				}

			} else if ( Data::isTrue( $args['virtualProducts'] ) ) {
				$queryArgs['meta_query'][] = [
					'key' => '_virtual',
					'value' => 'yes',
					'compare' => '==',
				];
			}



		} else {
			if ( ! Data::isTrue( $args['downloadableProducts'] ) ) {
				$queryArgs['meta_query'][] = [
					'key' => '_downloadable',
					'value' => 'yes',
					'compare' => '!=',
				];
			}

			if ( ! Data::isTrue( $args['virtualProducts'] ) ) {
				$queryArgs['meta_query'][] = [
					'key' => '_virtual',
					'value' => 'yes',
					'compare' => '!=',
				];
			}
		}

		if ( Data::isTrue( $args['filterByPrice'] ) ) {

	        if ( !empty( $args['startPrice'] ) ) {
	            $queryArgs['meta_query'][] = [
	                'key' => '_price',
	                'value' => $args['startPrice'],
	                'compare' => '>=',
					'type'    => 'NUMERIC',
	            ];
	        }

	        if ( !empty( $args['endPrice'] ) ) {
	            $queryArgs['meta_query'][] = [
	                'key' => '_price',
	                'value' => $args['endPrice'],
	                'compare' => '<=',
					'type'    => 'NUMERIC',
	            ];
	        }

	        if ( !empty( $args['startSalePrice'] ) ) {
	            $queryArgs['meta_query'][] = [
	                'key' => '_sale_price',
	                'value' => $args['startSalePrice'],
	                'compare' => '>=',
					'type'    => 'NUMERIC',
	            ];
	        }

	        if ( !empty( $args['endSalePrice'] ) ) {
	            $queryArgs['meta_query'][] = [
	                'key' => '_sale_price',
	                'value' => $args['endSalePrice'],
	                'compare' => '<=',
					'type'    => 'NUMERIC',
	            ];
	        }

		}

		return new \WP_Query( $queryArgs );
	}

	/**
	 * Renders preview for query params
	 *
	 * @param array $args
	 *
	 * @return array
	 */
    public function previewRecords( array $args = [] ) {
		$args = $this->prepare( $args );
		$availableRecords = $this->getRecords( $args );

		$records = [];

		if ( $availableRecords && $availableRecords->have_posts() ) {

			foreach( $availableRecords->posts as $post ) {
				$product = wc_get_product( $post->ID );
				if ( ! $product ) {
					continue;
				}

				$featuredImage = [];

				if( $featuredImageId = $product->get_image_id() ){
					$imageInfo = wp_get_attachment_image_src( $featuredImageId, 'full' );
					$featuredImage = [
						'id'     => $featuredImageId,
						'url'    => $imageInfo[0] ?? '',
						'width'  => $imageInfo[1] ?? '',
						'height' => $imageInfo[2] ?? '',
					];
				}

				$secondaryImage = [];
				$attachment_ids = $product->get_gallery_image_ids();
				if( !empty( $attachment_ids[0] ) ){
					$imageInfo = wp_get_attachment_image_src( $attachment_ids[0], 'full' );
					$secondaryImage = [
						'id'     => $attachment_ids[0],
						'url'    => $imageInfo[0]  ?? '',
						'width'  => $imageInfo[1] ?? '',
						'height' => $imageInfo[2] ?? '',
					];
				}

				$excerpt = !empty( $args['excerptLength'] ) ? Str::trimByChars( get_the_excerpt( $post->ID ), $args['excerptLength'] ) : get_the_excerpt( $post->ID );
				$postInfo = [
					'id'        => $post->ID,
					'title'     => $product->get_title(),
					'url'       => get_permalink( $post->ID ),
					'featuredImage' => $featuredImage,
					'secondaryImage' => $secondaryImage,
					'date'      => get_the_date('', $post->ID ),
					'excerpt'   => $excerpt,
					'content'   => $product->get_description(),
					'taxonomies'=> [],
					'shortDescription' => $product->get_short_description(),
					'price' => wc_price( $product->get_regular_price() ) . $product->get_price_suffix(),
					'salePrice' => $product->is_on_sale() ? wc_price( $product->get_sale_price() ) . $product->get_price_suffix() : '',
					'onSale'=> $product->is_on_sale(),
					'ratingAverage' => $product->get_average_rating(),
					'ratingCount' => $product->get_rating_count(),
					'reviewCount' => $product->get_review_count(),
					'sku' => $product->get_sku(),
					'stockStatus'  => $this->getStockStatus( $product ),
					'stockStatusClass' => $product->is_in_stock() ? 'in-stock' : 'out-of-stock',
					'isInStock'    => $product->is_in_stock(),
					'stockQuantity'=> $product->get_stock_quantity()

				];

				$taxonomies = get_object_taxonomies( $args['postType'], 'objects' );

				if ( !empty( $taxonomies ) ) {
					foreach( $taxonomies as $taxonomySlug => $taxonomy ) {
						$taxonomyInfo = [
							"id"    => $taxonomySlug,
							"label" => $taxonomy->label,
							"terms" => []
						];

						if ( $terms = wp_get_post_terms( $post->ID, $taxonomySlug ) ) {
							foreach( $terms as $term ) {
								$taxonomyInfo[ "terms" ][] = [
									'id' => $term->term_id,
							        'value' => $term->slug,
							        'label' => $term->name,
									'link' => get_term_link( $term->term_id )
								];
							}
						}

						$postInfo['taxonomies'][] = $taxonomyInfo;
					}
				}

				$records[] = $postInfo;
			}
		}

		return $records;
    }
	
	/**
	 * Retrieves product stock status
	 *
	 * @param $product
	 *
	 * @return mixed|void
	 */
	protected function getStockStatus( $product ){
		if ( $product->is_on_backorder() ) {
			$stock_html = __( 'On backorder', 'depicter' );
		} elseif ( $product->is_in_stock() ) {
			$stock_html = __( 'In stock', 'depicter' );
		} else {
			$stock_html = __( 'Out of stock', 'depicter' );
		}
		return apply_filters( 'woocommerce_admin_stock_html', $stock_html, $product );
	}
}
