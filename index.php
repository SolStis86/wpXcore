<?php $theme = new WPX_Theme; ?>
<!DOCTYPE html>
<html>
	<head>
		<?php wp_head(); ?>
	</head>
	<body>
		<?php 
			$theme->header();
			$theme->content();

/*			$test = new WPX_DB;

print_r(
	 $test->createTable('test_table', function($table){
			$table->increments('id');
			$table->text('text')->null();
			$table->integer('test_integer');
			return $table;
		})
	 );

$model = new WPX_mForms;

var_dump($model->update(5, ['id' => 5, 'name' => 'jimmy']));
*/
			$theme->getFooter();
			//print_r(get_queried_object());

		?>
	<?php wp_footer(); ?>
	</body>
</html>