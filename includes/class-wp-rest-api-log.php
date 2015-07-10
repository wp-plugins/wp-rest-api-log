<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'WP_REST_API_Log' ) ) {

	class WP_REST_API_Log {


		public function plugins_loaded() {

			// filter that is called by the REST API right before it sends a response
			add_filter( 'rest_pre_serve_request', array( $this, 'rest_pre_serve_request' ), 9999, 4 );

			add_filter( 'wp-rest-api-log-bypass-insert', function( $bypass_insert, $result, $request, $rest_server ) {
				// an example of disabling logging for specific requests

				if ( stripos( $request->get_route(), '/wp-rest-api-log') !== false ) {
					$bypass_insert = true;
				}

				return $bypass_insert;

			}, 10, 4 );

		}


		public function rest_pre_serve_request( $served, $result, $request, $rest_server ) {

			// allow specific requests to not be logged
			$bypass_insert = apply_filters( WP_REST_API_Log_Common::$plugin_name . '-bypass-insert', false, $result, $request, $rest_server );
			if ( $bypass_insert ) {
				return $served;
			}

			$args = array(
				'ip_address'            => $_SERVER['REMOTE_ADDR'],
				'route'                 => $request->get_route(),
				'method'                => $request->get_method(),
				'status'                => $result->get_status(),
				'request'               => array(
					'body'                 => $request->get_body(),
					'headers'              => $request->get_headers(),
					'query_params'         => $request->get_query_params(),
					'body_params'          => $request->get_body_params(),
					),
				'response'              => array(
					'body'                 => $result,
					'headers'              => $this->get_response_headers( $result ),
					),
				);


			do_action( WP_REST_API_Log_Common::$plugin_name . '-insert', $args );

			return $served;

		}


		private function get_response_headers( $result ) {
			// headers_list returns an array of headers like this: Content-Type: application/json;
			// we want a key/value array
			if ( function_exists( 'headers_list' ) ) {
				$headers = array();
				foreach ( headers_list() as $header ) {
					$header = explode( ':', $header );
					if ( count( $header ) > 1 ) {
						$headers[ $header[0] ] = trim( $header[1] );
					}
				}
				return $headers;
			} else {
				return $result->get_headers();
			}
		}


	} // end class

}
