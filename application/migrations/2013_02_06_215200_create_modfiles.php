<?php

class Create_Modfiles {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'modfiles', function( $table ) {
			$table->increments( 'id' );

			$table->integer( 'mod_id' )->unsigned( );
			$table->foreign( 'mod_id' )->references( 'id' )->on( 'mods' );

			$table->string( 'version', 80 );
			$table->string( 'md5', 32 );

			$table->timestamps( );
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop( 'modfiles' );
	}

}