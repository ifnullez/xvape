<?php
namespace App\CarbonFieldsOverrides;

use Carbon_Fields\Field\Association_Field;

class ExtendAssociationField extends Association_Field {
    /**
	 * Modify the clauses for the SQL request of the WP_Term_Query.
	 *
	 * @access public
	 *
	 * @param  array  $clauses
	 * @return array
	 */
	public function get_term_options_sql_clauses( $clauses ) {
		unset( $clauses['order'], $clauses['limits'] );

		return $clauses;
	}
}