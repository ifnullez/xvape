<?php
namespace App\ThemeFields\Icons;

use Carbon_Field_Icon\Providers\Icon_Provider_Interface;

class IconsProvider implements Icon_Provider_Interface {
	public function parse_options() {
		$icons_classes_list = json_decode(file_get_contents(dirname(__FILE__) . '/icons_list.json'), true);
		$options = [];
		if(!empty($icons_classes_list)){
			foreach($icons_classes_list as $icon_class){
				$options[ $icon_class ] = [
					'value'        => $icon_class,
					'name'         => ucfirst(str_replace(['-', '_'], [' ', ' '], $icon_class)),
					'class'        => "bi bi-{$icon_class}",
					'search_terms' => explode('-', $icon_class),
					'provider'     => 'bootstrap_5_icons',
				];
			}
		}
		return $options;
	}

}